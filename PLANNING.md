# Planification

Ce document se structure en deux parties complémentaires :

1. Découpage fonctionnel :
   - Objectif : Décrire, du point de vue métier, les fonctionnalités attendues de l’API.
   - Contenu :
     - Des user stories rédigées en anglais au format Gherkin, couvrant les opérations essentielles liées aux événements (création, édition, suppression, consultation individuelle et listée, filtrage par statut, pagination).
     - Des scénarios dédiés à l’authentification, tels que la connexion (login), le rafraîchissement du token et l’accès aux ressources protégées, afin de garantir que la sécurité et l’accès aux données se font de manière contrôlée.

2. Découpage technique :
   - Objectif : Détail des tâches techniques nécessaires pour mettre en œuvre le projet, depuis la phase d’initiation jusqu’à la mise en production.
   - Contenu :
     - Initialisation du projet : Création du projet Symfony, mise en place du contrôle de version, et intégration de la sécurité (configuration du bundle de sécurité, gestion des JWT, création de l’entité User, etc.).
     - Environnement local : Utilisation de Symfony CLI pour lancer l’application, avec la conteneurisation uniquement des services tiers (base de données, Redis, Mailhog) via Docker Compose.
     - CI/CD : Mise en place d’un pipeline d’intégration continue (tests automatisés, build et push d’images Docker) afin de garantir la qualité du code et faciliter les déploiements.
     - Déploiement en environnement Kubernetes local : Simulation d’un environnement de production avec minikube, incluant le déploiement de l’application via Helm, la configuration de l’ingress, la gestion des secrets, et la mise en place d’un autoscaling via HPA.
     - Logging & Monitoring : Déploiement et configuration d’une solution d’agrégation de logs (ex. Loki + Promtail) ainsi que d’un système de monitoring (Prometheus et Grafana) pour surveiller la performance et la santé de l’application.

## Découpage fonctionnel

### Identification d'un utilisateur

```gherkin
Feature: User Login
  As a user
  I want to log in with valid credentials
  So that I can receive a JWT token to access protected resources

  Scenario: Successful login
    Given I am a registered user with valid credentials
    When I send a login request with my email and password
    Then I receive a valid JWT token

  Scenario: Unsuccessful login with invalid credentials
    Given I am a user with invalid credentials
    When I send a login request with an incorrect email or password
    Then I receive an error message indicating invalid credentials
```

### Rafraichissement de l'authentification

```gherkin
Feature: Token Refresh
  As a user
  I want to refresh my authentication token when it is nearing expiration
  So that I can continue accessing protected resources without interruption

  Scenario: Successfully refreshing the token
    Given I have a valid refresh token
    When I send a token refresh request with my refresh token
    Then I receive a new valid JWT token
```

### Création d'un évènement

```gherkin
Feature: Create a marketing event
  As a user
  I want to create a marketing event with a title, description, dates, and status
  So that I can plan my campaigns and organize my marketing actions

  Scenario: Successfully creating an event
    Given I am an authenticated user
    When I send a request to create an event with a title, description, start date, end date, and status
    Then the event is created
    And I receive its details with a unique identifier
```

### Modification d'un évènement

```gherkin
Feature: Edit an existing event
  As a user
  I want to modify the details of an event
  So that I can update its information as needed

  Scenario: Successfully updating an event
    Given I am an authenticated user
    And an existing event I previously created
    When I send a request to update this event with new details
    Then the event is updated
    And I receive the updated event details
```

### Suppression d'un évènement

```gherkin
Feature: Delete an event
  As a user
  I want to delete an event
  So that I can keep my event list relevant

  Scenario: Successfully deleting an event
    Given I am an authenticated user
    And an existing event I previously created
    When I send a request to delete this event
    Then the event is no longer available
```

### Récupération des évènements

```gherkin
Feature: Retrieve events
  As a user
  I want to get a list of events I created
  So that I can visualize my campaign planning

  Scenario: Successfully retrieving events
    Given I am an authenticated user
    And there are existing events I previously created
    When I send a request to retrieve the list of events
    Then I receive the list of events I created
```

### Filtrage des évènements par statut

```gherkin
Feature: Filter events by status
  As a user
  I want to filter the events by status (draft, published, archived)
  So that I can easily find the relevant ones

  Scenario: Filtering events by "published" status
    Given there are events with different statuses
    And I am an authenticated user
    When I send a request to retrieve only events with the status "published"
    Then I receive only the events with that status
```

### Pagination des évènements

```gherkin
Feature: Paginate the list of events
  As a user
  I want to paginate the event list
  So that I can avoid loading too much data at once

  Scenario: Successfully retrieving paginated events
    Given there are multiple events
    And I am an authenticated user
    When I send a request to retrieve a specific page with a defined limit of events
    Then I receive only the requested number of events
    And I receive pagination metadata
```

### Récupération d'un évènement

```gherkin
Feature: Retrieve a single event
  As a user
  I want to retrieve the details of a specific event I created
  So that I can view its information

  Scenario: Successfully retrieving an event by ID
    Given I am an authenticated user
    And an existing event I previously created
    When I send a request to retrieve this event by its ID
    Then I receive the details of the event
```

## Découpage technique

### 1. Initialisation du projet

- [ ] Configuration de l'environnement local de développement via Nix
- [ ] Création de l'application Symfony en version 6.4
- [ ] Configuration des services tiers via Docker Compose
  - Service PostgreSQL
- [ ] Installation et configuration des paquets nécessaires aux besoins du projet
  - Doctrine
  - Monolog
  - Validator
  - Security
  - Uid
  - PHPUnit
  - Behat
- [ ] Configuration l'analyse statique et du formatage de la syntaxe
  - PHPStan avec les extensions Symfony, Doctrine, PHPUnit & Behat
  - PHP-CS-Fixer

### 2. Configuration de l'authentification

- [ ] Installer et configurer le bundle `lexik/jwt-authentication-bundle`
- [ ] Création de l'entité `User` permettant de porter les informations d'authentification
- [ ] Implémentation des user stories couvrant l'authentification

### 3. Configuration de l'intégration continue et de la livraison continue

- [ ] Création d'un workflow GitHub Actions permettant la mise en place de l'intégration continue
  - Validation de la syntaxe
  - Analyse statique du projet
  - Exécution des tests unitaires via PHPUnit
  - Exécution des tests de comportement via Behat
- [ ] Création d'un workflow GitHub Actions permettant le déclenchement de la livraison continue
  - Création d'une image Docker
  - Publication de l'image Docker sur le registre Docker GitHub.

### 4. Configuration locale de l'environnement Kubernetes

- [ ] Configuration d'un environnement local Kubernetes avec minikube
- [ ] Configuration des resources nécessaires au fonctionnement de l'application
  - Base de données (StatefulSet + PersistentVolume)
  - Reverse Proxy (Nginx Ingress Controller)
- [ ] Création d'une chart Helm pour l'application
  - DeploymentSet
  - Service
  - ConfigMap
  - Secrets
- [ ] Modification du workflow de livraison continue pour mettre à jour la configuration Helm

### 5. Implémentation du système d'évènements marketing

- [ ] Création de l'entité `Event` permettant de porter les informations d'un évènement
- [ ] Implémentation des user stories couvrant les fonctionnalités

### 6. Configuration de la collection des logs

- [ ] Configuration de la collection et du stockage des logs
  - Collection des logs via `Promtail`
  - Stockage des logs via Loki
- [ ] Visualisation des logs via Grafana

### 7. Configuration de la surveillance de Kubernetes et de l'application

- [ ] Installation de l'opérateur Prometheus
- [ ] Configuration des agents de métriques
  - Noeud k8s via `blackbox-exporter`
  - Reverse Proxy via `nginx-prometheus-exporter` & `php-fpm_exporter`
  - Base de données via `postgres_exporter`
- [ ] Visualisation des métriques via Grafana

### 8. Mise en place de l'auto-scalabilité avec Kubernetes

- [ ] Configuration de Horizontal Pod Autoscaling
- [ ] Stress test de l'auto-scalabilité avec l'outil k6
