# 🌐 Intranet Social d'Entreprise

Bienvenue dans le projet **Intranet Social** développé avec **Symfony 6**, **Bootstrap 5**, et des fonctionnalités sociales complètes.

Ce projet permet de créer un réseau social interne pour entreprise, avec :
- ✅ Messagerie privée
- ✅ Publications avec likes
- ✅ Commentaires avec likes
- ✅ Gestion de groupes de travail publics / privés
- ✅ Forum par groupe avec notifications
- ✅ Annuaire des utilisateurs
- ✅ Interface Admin complète pour gestion des utilisateurs et des groupes
- ✅ Responsive design et ergonomie soignée

---

## 🚀 Prérequis

Avant de commencer, assurez-vous d'avoir :

- ✅ PHP >= 8.1
- ✅ Composer
- ✅ Node.js >= 14.x
- ✅ Yarn ou npm
- ✅ Symfony CLI (optionnel mais recommandé)

---

## 📥 Installation locale

### 1. Clonez le projet

```bash
git clone https://github.com/stshauke/intranet_social.git
cd votre-depot

### 2.  Installez les dépendances PHP
composer install


### 3.  Installez les dépendances front-end Si vous utilisez npm :
npm install

### 4.  Compilez les assets (CSS / JS)
npm run dev

### Pour la production
npm run build

### 5. Configurez votre base de données
DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/intranet_social?serverVersion=8.0"


php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

### 7. Chargez les données de test (fixtures) : c'est optionnel car tu as déjà les données dans la base de données
php bin/console doctrine:fixtures:load

### Cela créera des utilisateurs fictifs et un admin pour commencer.

### 8. Lancez le serveur local Symfony
symfony server:star -d

### Visitez : http://127.0.0.1:8000


🛠 Fonctionnalités disponibles
✅ Gestion complète des utilisateurs

✅ Profil utilisateur avec photo

✅ Messagerie privée avec édition/suppression de messages

✅ Groupes de travail (public/privé) avec forum interne

✅ Système de notifications (likes, messages de groupe, etc.)

✅ Interface Admin pour gérer les utilisateurs et les groupes

✅ Pagination et ergonomie fluide