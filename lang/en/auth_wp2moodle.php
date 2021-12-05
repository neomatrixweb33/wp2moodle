<?php

$string['pluginname'] = 'Wordpress 2 Moodle';

$string['settings_heading'] = 'Wordpress 2 Moodle';
$string['settings_description'] = 'Utilise les détails utilisateur Wordpress pour créer un utilisateur et se connecter à Moodle (authentification unique unidirectionnelle)';

$string['settings_sharedsecret'] = 'Shared secret';
$string['settings_sharedsecret_desc'] = 'Clé de cryptage qui correspond à Wordpress';

$string['settings_timeout'] = 'Délai expiration du lien';
$string['settings_timeout_desc'] = 'Minutes avant que le lien entrant ne soit considéré comme invalide (utilisez 0 pour aucune expiration)';

$string['settings_logoffurl'] = 'URL de déconnexion';
$string['settings_logoffurl_desc'] = 'URL vers laquelle rediriger si utilisateur appuie sur Déconnexion (facultatif)';

$string['settings_autoopen'] = 'Cours à ouverture automatique';
$string['settings_autoopen_desc'] = 'Ouvrir automatiquement le cours après une authentification réussie';

$string['settings_updateuser'] = 'Mettre à jour les champs du profil utilisateur avec les valeurs Wordpress ?';
$string['settings_updateuser_desc'] = 'Lorsque OUI, les champs du profil utilisateur (prénom, nom, e-mail, numéro identification) sont mis à jour pour utiliser les valeurs fournies. Désactivez cette option si vous souhaitez laisser utilisateur gérer les champs de son profil de manière indépendante.';

$string['settings_redirectnoenrol'] = 'Ne rediriger utilisateur que vers le cours ?';
$string['settings_redirectnoenrol_desc'] = 'Lorsque OUI, inscription au cours est ignorée. utilisateur sera toujours redirigé vers la page accueil du cours (il est pas remplacé par ailleurs).';

$string['settings_firstname'] = 'Prénom (si vide)';
$string['settings_firstname_desc'] = 'Si aucun prénom est spécifié par Wordpress, utilisez cette valeur';

$string['settings_lastname'] = 'Nom (si vide)';
$string['settings_lastname_desc'] = 'Si aucun nom de famille est spécifié par Wordpress, utilisez cette valeur';

$string['settings_matchfield'] = 'Champ utilisé pour faire correspondre';
$string['settings_matchfield_desc'] = 'Lors de la création ou de la correspondance utilisateurs, utilisez ce champ de base de données pour faire correspondre les enregistrements (par défaut : idnumber)';

$string['settings_idprefix'] = 'Préfixe du numéro identification utilisateur (idnumber)';
$string['settings_idprefix_desc'] = 'Valeur de chaîne facultative à stocker devant le numéro identification pour éviter les conflits (par défaut : wp2m).';

$string['notloggedindebug'] = 'La tentative de connexion a échoué. Raison: {$a}';
$string['loginerror_invaliddomain'] = 'Votre adresse e-mail non autorisée sur ce site.';
