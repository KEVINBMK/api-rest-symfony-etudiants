# Guide d'installation rapide

Ce guide sert au membre du groupe qui aura un PC.

## 1. Cloner le projet

```bash
git clone https://github.com/KEVINBMK/api-rest-symfony-etudiants.git
cd api-rest-symfony-etudiants
```

## 2. Installer les dépendances

```bash
composer install
```

## 3. Préparer l'environnement

```bash
cp .env.example .env.local
```

Modifier la ligne `DATABASE_URL` dans `.env.local` selon MySQL local.

Exemple XAMPP sans mot de passe :

```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/api_rest_symfony_etudiants?serverVersion=8.0.32&charset=utf8mb4"
```

## 4. Créer la base de données

```bash
php bin/console doctrine:database:create
```

## 5. Lancer les migrations

```bash
php bin/console doctrine:migrations:migrate
```

## 6. Lancer le serveur

```bash
symfony server:start
```

ou :

```bash
php -S 127.0.0.1:8000 -t public
```

## 7. Tester dans Postman

Base URL :

```text
http://127.0.0.1:8000/api
```

Ordre de test :

1. `POST /register`
2. `POST /login`
3. Copier le token reçu.
4. Mettre le token dans Authorization > Bearer Token.
5. Tester `GET /me`.
6. Créer un étudiant.
7. Créer un cours.
8. Créer une inscription.
