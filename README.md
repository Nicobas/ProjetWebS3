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
--------- Comment ça marche ?   -------------
Administration

Le site est composé d'une partie administration (route /superadmin) permettant :

Partie I : Administration
- Gestion des utilisateurs
- Gestion des voyages (ajouter des voyages de leurs affecter des admins)

L'utilisateur superadmin est défini dans le code du projet, sont mot de passe est "admin" 

Partie II : Utilisateur

Ce type de profil est consacré aux utilisateurs qui peuvent s'inscrire aux voyages auxquels ils sont autorisés (voyage ouvert ou restriction par adresse mail) et les consulter. 

Chaque voyage à un panel d'administration permettant 
(admins définis par le superadmin) aux admins autorisés de :
- Créer des voyages
- Modifier/Supprimer des voyages
- Gérer les restrictions par mail
- Gérer les utilisateurs : les désinscrire, mettre un commentaire ou modifier le statut ("Payé" par exemple)

	
