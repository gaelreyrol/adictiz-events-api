# Application de système d'évènement marketing

Ce répertoire contient les fichiers et l'application censés répondre aux exigences du test pour le poste de "Head of Engineering".

- [Document de planification](PLANNING.md)
- [Document de revue](REVIEW.md)

## Environnement de développement

### Pré-requis

Pour développer sur ce projet, vous aurez besoin de :

- PHP 8.3 & Composer 2.8
- Docker & Docker Compose
- [Symfony CLI](https://github.com/symfony-cli/symfony-cli)
- [kubectl](https://kubernetes.io/fr/docs/tasks/tools/install-kubectl/)
- [Helm](https://helm.sh/docs/intro/install/)
- [minikube](https://minikube.sigs.k8s.io/docs/start)
- [skaffold](https://skaffold.dev/docs/install/#standalone-binary)
- [chart-testing](https://github.com/helm/chart-testing)
- [Loki LogCLI](https://grafana.com/docs/loki/latest/query/logcli/getting-started/)

#### Direnv & Nix

Si vous utilisez Nix et Direnv, pour bénéficier de tous les pré-requis sauf Docker & Docker Compose, il vous suffit d'exécuter la commande suivante :

```shell
direnv allow
# ou
nix develop
```

### Configuration des variables d'environnement

Copiez le fichier .env en .env.local et modifiez-le si nécessaire :

```shell
cp .env .env.local
```

### Installation des dépendances

Une fois les pré-requis installés, il faut installer les dépendances liées à l'application :

```shell
composer install
```

### Configuration des clés

Enfin de permettre la génération et la vérification des tokens JWT, il faut préalable créer une paire de clé SSL :

```shell
bin/console lexik:jwt:generate-keypair
```

## Lancement de l'application

Pour permettre à l'application de disposer de ses services tiers, il faut exécuter la commande suivante :

```shell
docker compose up -d
```

Une fois les services tiers actifs et en cours d'exécution, il ne reste plus qu'à exécuter les migrations et lancer le serveur PHP :

```shell
bin/console doctrine:database:create --if-not-exists
bin/console doctrine:database:migrate
symfony server:start # -d pour lancer le serveur en arrière plan
```

Vous pouvez utiliser la commande suivante pour créer un utilisateur :

```shell
bin/console adictiz:user:create john@doe.com mystrongpassword
```

## Vérifications

Les commandes suivantes permettent :

- de formater le code avec PHP-CS-Fixer
- de valider les configurations Symfony.
- de s'assurer que le schéma de base de données est à jour.
- d'analyser le code avec PHPStan.

```shell
composer format # Formatage du code
composer lint # Validation des configurations et analyse du code
```

## Exécution des tests

Avant d'exécuter les tests, assurez-vous que le schéma de la base de données soit à jour :

```shell
bin/console doctrine:database:create --if-not-exists --env=test
bin/console doctrine:schema:drop --force --env=test
bin/console doctrine:schema:update --force --env=test
```

Pour lancer les tests unitaires :

```shell
composer test:unit
```

Pour lancer les fonctionnels :

```shell
composer test:behavior
```

## Arrêt de l'application

Pour arrêter les conteneurs de services tiers :

```shell
docker composer stop
```

Si vous avez lancé le serveur PHP en arrière plan :

```shell
symfony server:stop
```

## Environnement local Kubernetes

Cette section décrit comment configurer un cluster local avec minikube, déployer l'environnement via Skaffold et vérifier que l'API répond correctement.

### Configuration du cluster minikube

Pour démarrer votre cluster minikube et activer l'addon Ingress (utile pour la gestion des routes HTTP externes), exécutez les commandes suivantes :

```shell
minikube start --bootstrapper=kubeadm \
  --extra-config=kubelet.authentication-token-webhook=true \
  --extra-config=kubelet.authorization-mode=Webhook \
  --extra-config=scheduler.bind-address=0.0.0.0 \
  --extra-config=controller-manager.bind-address=0.0.0.0
minikube addons enable ingress
```

### Déploiement de l'environnement

Le déploiement de l'application est automatisé grâce à Skaffold. Assurez-vous que votre fichier skaffold.yaml est correctement configuré. Ensuite, lancez la commande suivante :

```shell
skaffold run --port-forward=user
# Si vous souhaitez re-déployer à chaud l'environnement, privilégiez `skaffold dev`
```

Une fois le déploiement effectué, vérifier que l'API répond correctement avec les commandes suivantes :

```shell
# Appel direct à l'API
curl --resolve "adictiz-events-api.local:80:$(minikube ip)" -i http://adictiz-events-api.local

# Récupération d'un token JWT pour l'utilisateur k6@adictiz.com
export TOKEN=(curl -s --resolve "adictiz-events-api.local:80:$(minikube ip)" http://adictiz-events-api.local/api/login \
  -X POST \
  -H "Content-Type: application/json" \
  -d '{"username": "k6@adictiz.com", "password": "password"}' | jq -r .token)

# Récupération des évènements liés à l'utilisateur k6@adictiz.com
curl --resolve "adictiz-events-api.local:80:$(minikube ip)" http://adictiz-events-api.local/api/events \
  -X GET \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" | jq
```

### Collection des logs via Promtail et Loki

Pour vérifier la collection des logs, vous pouvez utiliser LogCLI pour contacter Loki et récupérer des logs de l'application :

```shell
export LOKI_ADDR=http://localhost:3100

logcli query '{app="adictiz-events-api"}' -f
```

Pour que la CLI puisse contacter Loki, il faut que l'environnement Kubernetes soit lancé et que la redirection de port vers le service Loki soit activé.

### Auto-scalabilité de l'environnement

Vérifier que la règle de métrique personnalisée à partir de métrique Prometheus est disponible :

```shell
kubectl get --raw "/apis/custom.metrics.k8s.io/v1beta1/namespaces/default/pods/*/phpfpm_active_processes_utilization" | jq .
```

### Montée en charge avec K6

Pour exécuter les tests de charge, lancer la commande suivante :

```shell
kubectl exec services/adictiz-events-api -it -- bin/console adictiz:user:create k6@adictiz.com password
kubectl exec services/adictiz-events-api -it -- bin/console doctrine:fixtures:load --group=k6 --append --no-interaction
cd kubernetes/helm/adictiz-events-api
helm test adictiz-events-api
```

Puis consulter les dashboards Grafana suivants :

<http://127.0.0.1:3000/dashboards>

- k6 Prometheus (Native Histograms)
- Horizontal Pod Autoscaler (HPA)
- Kubernetes PHP-FPM
- NGINX
