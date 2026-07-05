# Documentation rapide des endpoints

Base URL locale conseillee :

```text
http://127.0.0.1:8000/api
```

## 1. Authentification

### Créer un utilisateur

```http
POST /api/register
```

Body JSON :

```json
{
  "nom": "Administrateur",
  "email": "admin@example.com",
  "password": "password123"
}
```

### Se connecter

```http
POST /api/login
```

Body JSON :

```json
{
  "email": "admin@example.com",
  "password": "password123"
}
```

Copier ensuite le token reçu.

### Profil connecté

```http
GET /api/me
Authorization: Bearer TOKEN
```

## 2. Étudiants

```http
GET /api/etudiants
POST /api/etudiants
GET /api/etudiants/{id}
PUT /api/etudiants/{id}
DELETE /api/etudiants/{id}
```

Body création étudiant :

```json
{
  "matricule": "ETU-001",
  "nom": "Bitubisha",
  "postnom": "Mbemba",
  "prenom": "Kevin",
  "email": "kevin@example.com",
  "telephone": "+243000000000"
}
```

## 3. Cours

```http
GET /api/cours
POST /api/cours
GET /api/cours/{id}
PUT /api/cours/{id}
DELETE /api/cours/{id}
```

Body création cours :

```json
{
  "code": "INFO-101",
  "intitule": "Ingénierie logicielle",
  "description": "Cours sur la conception logicielle",
  "credits": 4
}
```

## 4. Inscriptions

```http
GET /api/inscriptions
POST /api/inscriptions
GET /api/etudiants/{id}/cours
GET /api/cours/{id}/etudiants
DELETE /api/inscriptions/{id}
```

Body création inscription :

```json
{
  "etudiant_id": 1,
  "cours_id": 1
}
```

## Ordre de test conseillé

1. POST `/api/register`
2. POST `/api/login`
3. Copier le token.
4. GET `/api/me`
5. POST `/api/etudiants`
6. POST `/api/cours`
7. POST `/api/inscriptions`
8. GET `/api/etudiants/1/cours`
9. Refaire POST `/api/inscriptions` pour montrer l'erreur de double inscription.
10. Tester GET `/api/etudiants` sans token pour montrer la sécurité.
