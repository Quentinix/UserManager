User Manager
===========

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/124dfab578544851bdef8c05fe0a6bc8)](https://www.codacy.com/app/Quentinix/UserManager?utm_source=github.com&utm_medium=referral&utm_content=Quentinix/UserManager&utm_campaign=badger)
[![Codacy Badge](https://api.codacy.com/project/badge/Coverage/802287394f6048669bb07ad3c294e13b)](https://www.codacy.com/app/Quentinix/UserManager?utm_source=github.com&utm_medium=referral&utm_content=Quentinix/UserManager&utm_campaign=Badge_Coverage)


Gestionnaire d'utilisateurs utilisables pour les sites web. 

----------
Pres-requis
-------
PHP version 5.6.31 minimum
Serveur MariaDB 10.1 minimum ou MySQL 5.7 minimum

Installation
----------------

Installation par Composer : **composer create-project quentinix/usermanager**
> **Attention:**
> Si l'installation est faite par la commande : "composer require quentinix/usermanager", la base de données ne sera pas installée !
> Pour que cela soit le cas, il faut le faire manuellement.
> - Configurer le ficher Config.php qui doit se trouver dans le dossier "vendor/quentinix/usermanager"
> Modifiez le l'adresse IP, le nom d'utilisateur et le mot de passe.
> - Exécuter le script SQL qui doit se trouver dans le dossier "vendor/quentinix/" directement dans votre base MariaDB ou MySQL.
