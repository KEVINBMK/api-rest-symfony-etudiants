# API REST Symfony — Gestion des étudiants, cours et inscriptions

Ce dépôt contient un backend API REST sécurisé avec Symfony pour gérer des étudiants, des cours et les inscriptions des étudiants aux cours.

Le projet ne contient pas de frontend. La démonstration se fait avec Postman.

## Technologies

- PHP 8.2 ou plus
- Symfony 7
- MySQL
- Doctrine ORM
- JWT simple avec HMAC SHA-256
- Postman pour les tests

## Modules

1. Authentification : inscription, connexion, profil connecté.
2. Étudiants : création, liste, détail, modification, suppression.
3. Cours : création, liste, détail, modification, suppression.
4. Inscriptions : inscrire un étudiant à un cours, afficher les cours d’un étudiant, afficher les étudiants d’un cours, empêcher la double inscription.

## Installation locale

```bash
composer install
cp .env.example .env.local
```

Modifier `DATABASE_URL` dans `.env.local` selon votre MySQL local.

Créer la base de données et exécuter les migrations :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Lancer le serveur :

```bash
symfony server:start
```

ou :

```bash
php -S 127.0.0.1:8000 -t public
```

## Endpoints principaux

### Authentification

- `POST /api/register`
- `POST /api/login`
- `GET /api/me`

### Étudiants

- `GET /api/etudiants`
- `POST /api/etudiants`
- `GET /api/etudiants/{id}`
- `PUT /api/etudiants/{id}`
- `DELETE /api/etudiants/{id}`

### Cours

- `GET /api/cours`
- `POST /api/cours`
- `GET /api/cours/{id}`
- `PUT /api/cours/{id}`
- `DELETE /api/cours/{id}`

### Inscriptions

- `GET /api/inscriptions`
- `POST /api/inscriptions`
- `GET /api/etudiants/{id}/cours`
- `GET /api/cours/{id}/etudiants`
- `DELETE /api/inscriptions/{id}`

## Utilisation du token dans Postman

Après connexion, copier le token reçu puis l’utiliser dans Postman :

```text
Authorization > Type > Bearer Token
```

Toutes les routes sauf `/api/register` et `/api/login` sont protégées.

## Réponses JSON

Succès :

```json
{
  "success": true,
  "message": "Opération effectuée avec succès",
  "data": {}
}
```

Erreur :

```json
{
  "success": false,
  "message": "Données invalides",
  "errors": {}
}
```

## Ordre de démonstration conseillé

1. Créer un utilisateur.
2. Se connecter.
3. Copier le token JWT.
4. Tester `/api/me`.
5. Ajouter un étudiant.
6. Ajouter un cours.
7. Inscrire l’étudiant au cours.
8. Afficher les cours de l’étudiant.
9. Montrer l’erreur de double inscription.
10. Montrer une route protégée sans token.
