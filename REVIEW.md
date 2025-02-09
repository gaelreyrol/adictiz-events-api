# Revue

Ce document récapitule les choix techniques adoptés et compromis pour le projet, puis propose des pistes d'évolutions.

## Architecture Applicative

### Structure du code

- Utilisation du framework **Symfony 6** et de l'ORM **Doctrine**.
- **Séparation des responsabilités** avec des contrôleurs, services et repositories distincts.
- **Utilisation des DTOs et Value Objects** pour encapsuler les données et améliorer la maintenabilité.
- Gestion des erreurs avec **des exceptions personnalisées** pour une meilleure robustesse.

### Sécurité et Authentification

- **Authentification JWT** avec LexikJWTAuthenticationBundle.
- **Firewall sécurisé** pour distinguer les accès (`/api/login` ouvert, `/api/*` protégé).
- **Utilisation de voters Symfony** pour gérer les permissions sur les événements.

### Gestion des données

- Base de données **PostgreSQL**.
- **Utilisation de Doctrine avec Criteria API** pour un filtrage flexible.
- **Pagination optimisée avec Doctrine Paginator** pour éviter une surcharge mémoire.

## Tests et Validation

### Analyse statique et Formatage du code

- **PHPStan** est utilisé pour l’analyse statique afin de détecter les erreurs potentielles et garantir un code robuste.
- **PHP-CS-Fixer** est employé pour maintenir une cohérence dans le style de code et appliquer les standards PSR.

### Tests unitaires avec PHPUnit

- Tests unitaires couvrant principalement les cas nominaux simples.
- Ces tests permettent d'assurer la non-régression et facilitent l'extension de la couverture de tests.

### Tests fonctionnels avec Behat

- Utilisation de **Behat, Mink et Behatch** pour tester l’API en conditions réelles.
- Simulation de requêtes API et validation des réponses JSON.
- Isolation des tests avec **DAMA\DoctrineTestBundle**.

### Tests de charge avec k6

- Déploiement de **k6-operator** sur Kubernetes.
- **Tests simulant plusieurs requêtes concurrentes** appelant l’API pour lister les événements d’un même utilisateur.
- Intégration des résultats dans **Prometheus/Grafana**.

## Déploiement avec Kubernetes et CI/CD avec GitHub Actions

### Intégration continue et Livraison continue

- Utilisation de **GitHub Actions** pour automatiser les tests, la validation du code et le déploiement.
- **Pipeline CI/CD structuré** comprenant :
    - Exécution des tests **PHPUnit** et **Behat**.
    - Analyse statique avec **PHPStan** et formatage du code avec **PHP-CS-Fixer**.
    - Construction et publication d'une image Docker sur **GitHub Container Registry.**

### Structure du déploiement

- Services séparés pour **PHP-FPM, Nginx et PostgreSQL**.
- Séparation des valeurs en `values.yaml` pour les dépendances et la configuration principale, améliorant ainsi la lisibilité et la gestion des configurations **Helm**.

### Auto-scaling et Performance

- **Horizontal Pod Autoscaler (HPA)** basé sur une règle de métrique personnalisée qui permet de scaler en fonction du ratio moyen des processus **PHP-FPM** actifs par rapport au total disponible.

### Logs et Monitoring

- Collecte des logs via **Promtail** et **Loki**.
- Dashboards **Grafana** pour **PostgreSQL**, **Nginx**, **PHP-FPM** et **k6**.
- Intégration de **Prometheus** et **Alertmanager** pour surveiller les performances.

## Compromis techniques

- **Utilisation de Direnv et Nix** : La combinaison de Direnv et Nix permet de faciliter l'installation des dépendances du projet mais est moins accessible techniquement pour les non initiés.
- **Utilisation d'une architecture logicielle simple** : Utilisation d'une structure **MVC** dans la mesure où les spécifications techniques restent simples, permettant ainsi de se concentrer sur le fonctionnel et l'environnement technique.
- **Utilisation extensive des charts Helm** : Réduction du temps de mise en place des services tiers tels que le reverse proxy, la base de données, la collection de logs et le monitoring.
- **Séparation PHP-FPM et Nginx en pods distincts** : Permet une meilleure scalabilité, mais entraîne une complexité supplémentaire en termes de gestion du trafic entre les pods.
- **Utilisation de GitHub Actions pour la CI/CD** : Intégration fluide avec GitHub, mais moins personnalisable qu’une solution auto-hébergée comme GitLab CI/CD.
- **Utilisation de Minikube pour les tests locaux** : Permet un développement sans coût cloud supplémentaire. **Skaffold** permettant une intégration facilitée.
- **Logs stockés dans Loki** : Choisi pour sa faible consommation de ressources par rapport à **Elasticsearch** et son intégration native avec **Grafana**.

## Évolutions futures

- Ajouter un **refresh token** pour **JWT** afin d’éviter les expirations trop fréquentes.
- Renforcer la gestion des erreurs **API** avec des codes **HTTP** plus précis et des logs enrichis.
- Tester d'autres runtimes **Symfony** tels que **Swoole** ou **FrankenPHP** pour améliorer la performance et la scalabilité de l’API.
- Mettre en place un **cycle de release Helm** automatique à l'aide des **Github Actions**.
- **Exécuter les tests** en CI dans un environnement **Kubernetes** via **Kind (Kubernetes in Docker)**.
- **Adopter une approche GitOps avec ArgoCD** pour automatiser et centraliser les déploiements Kubernetes.
- **Permettre le déploiement d’environnements de pré-production par pull requests** afin de valider les changements avant mise en production.
- **Faire évoluer l'architecture logicielle vers une architecture Hexagonale / Clean**, pour anticiper les besoins fonctionnels et faciliter la scalabilité, maintenabilité logicielles.
- **Génération d'une documentation OpenAPI** pour faciliter la documentation et la découverte de l'API.

### Sécurisation

- **Séparation en namespaces Kubernetes** : Organisation des ressources par environnement (ex : `monitoring`, `logging`, `application`) pour améliorer la sécurité et la gestion des accès.
- **Gestion avancée des secrets avec Vault** : Remplacement des `Secrets` Kubernetes par une intégration avec HashiCorp Vault pour sécuriser les informations sensibles.
- **Mise en place de politiques RBAC strictes** : Limiter l’accès aux ressources Kubernetes et réduire les risques de compromission.
- **Utilisation de Network Policies** : Restreindre les communications entre les pods et empêcher les accès non autorisés.
- **Mise en place de l'outil Dependabot** : Alerter de sécurité sur les dépendances contenant des failles de sécurité.

### Optimisation

- Affiner la configuration **PHP-FPM** en fonction de la topologie des nœuds de production.
- Améliorer l’auto-scaling **PHP-FPM** en prenant en compte les **requêtes en attente**.
- Intégrer un **système de cache** pour réduire la charge sur **PostgreSQL**.

### Observabilité

- **Intégration d'OpenTelemetry** pour collecter et analyser les traces des requêtes à travers les différents services.
- **Instrumentation des services Symfony** pour obtenir une meilleure visibilité sur les performances et les latences applicatives.

### Tests et Qualité logicielle

- Ajouter des **tests de charge avancés avec k6** (exécution de mutations en masse).
- **Renforcer la couverture de tests PHPUnit et Behat**, notamment sur les erreurs (`403`, `401`).
- **Collection des dépréciations** et correction continue à l'aide de **Rector**.
- Mise en place de l'outil **Dependabot** pour mettre à jour en continu les dépendances **Composer** et **Helm**.
