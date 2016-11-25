Trip'Esiea - Projet Web 4A
===========

--- Mise en production ---

Projet réalisé avec symfony 3

Prerequis :
 - PHP version 5.6 ou +
 - Composer installé sur le serveur
 - Git installé sur le serveur

Installation :
 - Clonner le git dans un repertoire du serveur
 - Ajouter un fichier parameters.yml dans app/config contenant les informations de connection à la BDD (le modèle du fichier est disponible dans app/config/parameters.yml.dist)
 - Modifier le fichier web/app.php à la ligne n°9 "$kernel = new AppKernel('prod', false);" en "$kernel = new AppKernel('prod', true);" afin de passer du mode de dev en mode production
 - Executer les commandes suivantes à la racine du projet :
	composer update
	chmod -R 777 .
	php bin/console doctrine:create:database
	php bin/console doctrine:schema:update --force
	php app/console assets:install web

 - Faire pointer le nom de dommaine/sous dommaine sur le répertoire web du projet
 - Enjoy !
	
