## ** Marche à suivre pour la mise en place du projet **

-   Version de Symfony : 5.4
-   Version de php : 7.2

Pour utiliser ce projet veuillez, cloner ce référentiel.
Rendez-vous dans le terminal de votre éditeur de code et tapez : git clone https://github.com/JrBka/JrBka_13_07_2023.git

Quelques modifications sont nécessaires avant que le projet soit fonctionnel :

-   installez composer à la racine du projet avec la commande : composer install.
-   créez un fichier .env.local à la racine du projet, copiez-y le contenu du fichier .env, décommentez la ligne DATABASE_URL et remplissez la avec les valeurs correspondant à votre installation.

Une fois le projet configuré lancez votre service de base donnée (mysql,postgresql...).
Créez maintenant la base donnée, structurez la et remplissez la avec les commandes suivante :

-   symfony console make:migration
-   symfony console d:m:m
-   symfony console d:f:l

Vous pouvez maintenant utiliser le projet en tapant la commande : 'symfony serve' dans votre terminal et en vous rendant dans votre navigateur à l'adresse suivant : https://127.0.0.1:8000/

Pour pouvoir utiliser les test, créez une base de donné de test, structurez la et remplissez la avec les commandes :

-   APP_ENV=test symfony console doctrine:database:create
-   APP_ENV=test symfony console doctrine:schema:update --force
-   APP_ENV=test symfony console d:f:l

Pour générer les tests et le code coverage utilisez la commande :

-   vendor/bin/phpunit --coverage-html public/test-coverage

Vous pouvez consulter le code coverage au format html dans votre navigateur à l'adresse suivante : https://127.0.0.1:8000/test-coverage/dashboard