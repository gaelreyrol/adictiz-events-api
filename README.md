# Application de système d'évènement marketing

Ce répertoire contient les fichiers et l'application censés répondre aux exigences du test pour le poste de "Head of Engineering".

- [Document de planification](PLANNING.md)

## Environnement de développement

### Pré-requis

Pour développer sur ce projet, vous aurez besoin de :

- PHP 8.3 & Composer 2.8
- Docker & Docker Compose
- Symfony CLI

#### Direnv & Nix

Si vous utilisez Nix et Direnv, pour bénéficier de PHP, Composer & Symfony CLI, il vous suffit d'exécuter la commande suivante :

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

## Lancement de l'application

Pour permettre à l'application de disposer de ses services tiers, il faut exécuter la commande suivante :

```shell
docker compose up -d
```

Une fois le conteneur de la base de données actif et en cours d'exécution, il ne reste plus qu'à lancer le serveur PHP :

```shell
symfony server:start # -d pour lancer le serveur en arrière plan
```

## Exécution des tests

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
