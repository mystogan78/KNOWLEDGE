# 📚 KNOWLEDGE  
> Plateforme d’apprentissage en ligne – Projet Symfony

![Symfony](https://img.shields.io/badge/Symfony-7.3-black?style=for-the-badge&logo=symfony)
![PHP](https://img.shields.io/badge/PHP-8.2-blue?style=for-the-badge&logo=php)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple?style=for-the-badge&logo=bootstrap)
![Sass](https://img.shields.io/badge/Sass-CSS-pink?style=for-the-badge&logo=sass)
![Stripe](https://img.shields.io/badge/Stripe-Payments-blueviolet?style=for-the-badge&logo=stripe)
![PHPUnit](https://img.shields.io/badge/Tests-PHPUnit-orange?style=for-the-badge&logo=php)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

---

## 🧾 Description du projet

**Knowledge** est une plateforme d’apprentissage en ligne permettant de :

- organiser le contenu en **catégories**, **cursus** et **leçons**  
- proposer des **leçons payantes** avec paiement via **Stripe**  
- gérer l’inscription, la connexion et les rôles (**utilisateur** / **administrateur**)  

Le projet a été développé dans le cadre d’une **formation de développeur web** avec **Symfony 7.3**.

---

## 🚀 Fonctionnalités principales

- 👤 **Gestion des utilisateurs**
  - Inscription avec formulaire sécurisé
  - Vérification d’email via **Symfony Mailer**
  - Connexion / déconnexion (sécurité Symfony)
  - Rôle administrateur pour la gestion du contenu

- 🧭 **Navigation par contenu**
  - Liste des **catégories** (informatique, jardinage, cuisine, musique…)
  - Liste des **cursus** liés à une catégorie
  - Liste des **leçons** associées à un cursus
  - Détails d’une leçon (titre, intro, prix, etc.)

- 🎨 **Interface utilisateur**
  - Design responsive avec **Bootstrap 5**
  - Styles personnalisés via **Sass** (compilé en `public/styles/app.css`)
  - **Logo** et **favicon** personnalisés intégrés dans la base de layout

- 💳 **Achat & paiement**
  - Création d’une entité `Purchase` lors du démarrage d’un achat
  - Paiement via **Stripe Checkout**
  - Gestion de l’état de l’achat : `pending` → `paid`
  - Page de succès / annulation après paiement

- 🛡️ **Sécurité & bonnes pratiques**
  - Protection **CSRF** sur les formulaires Symfony (`form_start` inclut le token)
  - Gestion des mots de passe avec `UserPasswordHasherInterface`
  - Routes protégées pour certaines actions (selon le rôle)

- ✅ **Tests**
  - Tests fonctionnels sur :
    - l’inscription (`RegistrationControllerTest`)
    - le processus d’achat (`PurchaseControllerTest`)
  - Exécution via `php bin/phpunit`

---

## 🛠️ Technologies utilisées

| Technologie        | Version   | Rôle                                  |
|--------------------|-----------|---------------------------------------|
| **Symfony**        | 7.3       | Framework backend principal           |
| **PHP**            | 8.2.12    | Langage backend                       |
| **Composer**       | 2.x       | Gestionnaire de dépendances PHP       |
| **Bootstrap**      | 5.3       | Frontend responsive                   |
| **Sass (Dart Sass)** | latest  | Préprocesseur CSS                     |
| **Stripe API**     | -         | Paiement en ligne                     |
| **Symfony Mailer** | -         | Envoi d’emails (ex : vérification)    |
| **PHPUnit**        | 11.x      | Tests unitaires / fonctionnels        |

---

## 🗂️ Structure du projet (simplifiée)

### `assets/`
- `styles/app.scss` – point d’entrée Sass
- `styles/_variables.scss` – couleurs, typos globales
- `styles/_base.scss` – base du layout, header, footer
- `styles/_category.scss` – styles des cartes catégories
- `styles/_course.scss` – styles des cartes cursus
- `styles/_lesson.scss` – styles des leçons & légende
- `controllers/` – éventuels fichiers JS (si utilisés)

### `config/`
- `packages/` – configuration des bundles (Mailer, Doctrine, etc.)
- `routes/` – définition des routes si YAML/PHP

### `public/`
- `styles/app.css` – CSS compilé depuis Sass
- `favicon/` – `favicon.ico`, `favicon_32x32.png`, etc.
- `images/` – logo, images éventuelles

### `src/`
- `Controller/`
  - `HomeController.php`
  - `CategoryController.php`
  - `CourseController.php`
  - `LessonController.php`
  - `PurchaseController.php` (gestion des achats / Stripe)
  - `RegistrationController.php` (inscription)
  - `SecurityController.php` (login / logout)
- `Entity/`
  - `User.php`
  - `Category.php`
  - `Course.php`
  - `Lesson.php`
  - `Purchase.php`
- `Repository/`
  - `CategoryRepository.php`
  - `CourseRepository.php`
  - `LessonRepository.php`
  - `PurchaseRepository.php`
- `Form/`
  - `RegistrationFormType.php` (formulaire d’inscription)
- `Security/`
  - `UserAuthenticator.php` (si utilisé)

### `templates/`
- `base.html.twig` – layout principal (navbar, footer, logo, favicon)
- `home/index.html.twig` – page d’accueil
- `category/index.html.twig` – liste des catégories
- `category/show.html.twig` – détail d’une catégorie + ses cursus
- `course/index.html.twig` – liste des cursus
- `course/show.html.twig` – détail d’un cursus + ses leçons
- `lesson/index.html.twig` – liste des leçons + légende de couleurs
- `lesson/show.html.twig` – détail d’une leçon
- `registration/register.html.twig` – formulaire d’inscription
- `security/login.html.twig` – page de connexion
- `purchase/success.html.twig` – succès de paiement
- `purchase/cancel.html.twig` – annulation

### Fichiers racine
- `.env` / `.env.local` – configuration locale (BBD, Stripe, Mailer…)
- `composer.json` – dépendances PHP
- `package.json` – scripts NPM (Sass, watch)
- `phpunit.dist.xml` – configuration des tests
- `README.md` – ce fichier

---

## ⚙️ Installation & lancement

```bash
# 1. Cloner le dépôt
git clone <url-du-depot-git>
cd knowledge

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances front (si Sass utilisé via NPM)
npm install

# 4. Compiler le Sass → CSS
npm run sass        # compilation unique
# ou
npm run sass:watch  # recompile automatiquement à chaque modification
```
## 🔧 Configuration de l’environnement

.env.local

``` bash
###> doctrine/doctrine-bundle ###
DATABASE_URL="mysql://user:password@127.0.0.1:3306/knowledge?serverVersion=8.0"
###< doctrine/doctrine-bundle ###

###> symfony/mailer ###
MAILER_DSN=smtp://localhost:1025
###< symfony/mailer ###

###> stripe ###
STRIPE_SECRET_KEY="sk_test_xxx"
###< stripe ###
```
## 🗄️ Base de données

``` bash

# Créer la base
php bin/console doctrine:database:create

# Appliquer les migrations
php bin/console doctrine:migrations:migrate

# (Optionnel) Charger des données de test
php bin/console doctrine:fixtures:load
```

## 🚀 Lancer le serveur

``` bash

symfony server:start
# ou
php -S localhost:8000 -t public

```
## 🧪 Tests

``` bash
php bin/phpunit

```

Exemple de test implémentés :

### RegistrationControllerTest :
verifie que l'inscription crée bien un compte utilisateur et redirige correctement

### PurchaseControllerTest :
vérifie le comportement du processus d'achat (création de purchase, routes, etc).

## 🔐 Sécurité & CSRF

Symfony protège automatiquement les formulaires via un token CSRF :

Le token est généré et inclus dans le formulaire via {{ form_start(...) }}.

Il est vérifié à la soumission du formulaire.

Cette protection est active par défaut pour les formulaires Symfony (ex : formulaire d’inscription).

Le projet utilise aussi :

des hashs de mot de passe (UserPasswordHasherInterface)

un système de rôles (USER / ADMIN) pour restreindre certaines pages

## 👨‍💻 Auteur

Ibrahim
Projet réalisé dans le cadre de la formation Développeur Web.

GitHub : mystogan78

## 📜 Licence

Ce projet est distribué sous licence MIT.
Vous êtes libre de le modifier et de le réutiliser à des fins pédagogiques ou personnelles.