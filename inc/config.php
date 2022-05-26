<?php
define('DATABASE_TYPE', 'mysql'); //mysql ou sqlite3

// inutile si sqlite3 est sélectionné
define('MYSQL_HOST', 'localhost');
define('MYSQL_DATABASE', 'URL_shortener');
define('MYSQL_USER', 'USER');
define('MYSQL_PASSWORD', 'PASSWORD');

//inutile si mysql est sélectionné
define('SQLITE3_FILE', './database.sqlite3');

define('SITE_NAME', 'MY SITE'); //Nom de votre site
define('DEFAULT_URL', 'https://localhost'); // url de votre site, omettre la barre oblique finale
define('URL_SIZE', 5); // La longueur de vos liens courts
define('WEB_THEME', 'dark'); // foncé ou clair
define('PUBLIC_INSTANCE', 'true'); // true pour autoriser les non connectées a cree un lien
define('ALLOW_SIGNIN', 'false'); // autorise l'inscription
?>
