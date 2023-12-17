# API
## Prérequis
Il vous faut git, symfony 6.3.4, php 8.1.12, composer ainsi que postman.

## Installation
Commencer par cloner le projet grâce à git dans le dossier de votre choix <br />
Ouvrez votre terminal de commande dans ce dossier <br />
Vous aller pouvoir installer les librairies requises par le projet grâce à composer avec la commande `composer install`<br />
vous allez ensuite pouvoir créer la base de données avec la commande suivante `php bin/console doctrine:database:create` <br />
Une fois la base de données crée, vous allez pouvoir créer les antités avec : `php bin/console doctrine:migrations:migrate` <br />
Et enfin vous pourrez la remplir avec : `php bin/console doctrine:fixtures:load`<br />

## Exéctuer votre première requête

⚠️ : Avant de faire votre première requête, il faudra génrér des certificats qui permettent d'assurer la sécurité
Pour le faire, rester dans votre terminal et exécuter la commande : `php bin/console lexik:jwt:generate-keypair`

Pour utiliser l'API, vous allez devoir vous connecter. Pour cela, commandez par démarrer le serveur symfony avec la commande : `symfony server:start`, n'oubliez pas de démarrer votre base de données également.
Une fois que le serveur est prêt, rendez-vous sur postman, effectuez la requête POST sur http://localhost:8000/api/login_check avec en body : 
```json
{
    "email": "admin@api.com",
    "password": "123"
}
```
Cela vous permet de récupérer un jeton d'identification, qu'il vous faudra passer dans les prochaines requêtes que vous voudrait faire en header avec bearer devant.
vous pouvez accéder à la documentation à l'adresse suivante une fois le serveur démarré : http://localhost:8000/api/doc <br />

Si, lors de l'éxécution d'une commande vous avez une erreur SSL, essayer de regénérer les certificats avec : `php bin/console lexik:jwt:generate-keypair -overwrite`
S'il vous est demandé Sodium, vous pouvez le trouver dans votre fichier php.ini, il faudra décommenter la ligne extension=sodium
