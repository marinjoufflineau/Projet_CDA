# LocalBonPlan — Projet CDA PHP / MongoDB

Application web de bons plans locaux développée en PHP 8 avec MongoDB.

## Points clés

- Interface utilisateur moderne et responsive
- Consultation, recherche et filtrage des bons plans
- Votes et commentaires pour les utilisateurs connectés
- Interface d'administration sécurisée
- CRUD complet sur les bons plans
- Authentification avec mots de passe hashés
- Protection CSRF sur les actions sensibles
- Architecture en couches : public / services / repositories / data
- Base MongoDB avec collections liées par ObjectId
- Tests unitaires avec PHPUnit

## Installation

```bash
composer install
cp .env.example .env
composer dump-autoload
php seed.php
php -S localhost:8000 -t public
```

Puis ouvrir :

```txt
http://localhost:8000
```

## Comptes de démonstration

Administrateur :

```txt
admin@local.test / admin
```

Utilisateur :

```txt
marin@local.test / marin
```

## Base de données

MongoDB local :

```txt
mongodb://localhost:27017
```

Base utilisée :

```txt
local_bon_plan
```

Collections :

- utilisateurs
- categories
- lieux
- bons_plans
- commentaires
- votes

## Tests

```bash
vendor/bin/phpunit tests
```

## Lecture CDA

Ce projet peut être présenté comme projet principal pour le titre CDA car il permet de démontrer :

- le développement d'interfaces utilisateur ;
- le développement de composants métier ;
- l'utilisation de composants d'accès aux données NoSQL ;
- la conception d'une architecture applicative organisée en couches ;
- la sécurisation d'une application web ;
- la préparation d'un plan de tests ;
- une démarche de gestion de projet structurée.


## Structure des répertoires

