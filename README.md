<h1> Projet 7 OpenClassrooms - API Bilemo </h1>
BileMo est une entreprise offrant toute une sélection de téléphones mobiles haut de gamme.

L'objectif de ce projet est de développer de la vitrine des téléphones mobiles de l’entreprise BileMo, en fournissant à toutes les plateformes qui le souhaitent l’accès au catalogue via une API (Application Programming Interface).
## Prérequis
- Symfony 6.3.4
- PHP 8.1.12
- Composer
- Git
- Postman (pour tester l'API)

## Installation
- Cloner le projet grâce à git dans le dossier de votre choix
- Ouvrez votre terminal de commande dans ce dossier :
    - Vous aller pouvoir installer les librairies requises par le projet grâce à composer avec la commande `composer install`
- Copier le fichier `.env` en le nommant `.env.local` et renseignez-y les informations d'accès à votre base de données
    - `DATABASE_URL="mysql://USER:PASSWORD@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4`
- Créer la base de données avec la commande suivante : `php bin/console doctrine:database:create`

Une fois la base de données créée, vous allez pouvoir effectuer les migrations en lançant la commande :

- `php bin/console doctrine:migrations:migrate`

Et enfin vous pourrez remplir la base données grâce aux fixtures en lançant la commande :

- `php bin/console doctrine:fixtures:load`

Avant de faire votre première requête, il faudra générer des certificats qui permettent d'assurer la sécurité

- Génération de vos clés publiques et privées :
    - Pour le faire, restez dans votre terminal et exécuter la commande : `php bin/console lexik:jwt:generate-keypair`
  
⚠️ Attention : Pensez à bien sauvegarder vos mots de passe !

## Authentification JWT

Pour utiliser l'API, vous allez devoir vous connecter.

Pour cela, commencez par démarrer le serveur symfony avec la commande : `symfony server:start`, n'oubliez pas de démarrer votre base de données également.

Une fois que le serveur est prêt, rendez-vous sur postman, effectuez la requête POST sur http://localhost:8000/api/login_check avec en body :

```json
{
    "email": "admin@api.com",
    "password": "123"
}
```

Cela vous permet de récupérer un jeton d'identification, qu'il vous faudra passer dans les prochaines requêtes que vous voudrait faire en header avec bearer devant.

⚠️ **Attention :**

Si, lors de l'exécution d'une commande vous avez une erreur SSL, essayer de regénérer les certificats avec :

`php bin/console lexik:jwt:generate-keypair -overwrite`
S'il vous est demandé d'installer Sodium, vous pouvez le trouver dans votre fichier `php.ini`, il faudra décommenter la ligne `extension=sodium`

## Documentation de l'API

Une fois le serveur démarré, vous trouverez toutes les informations d'utilisation de l'API en allant sur :

https://localhost:8000/api/doc
