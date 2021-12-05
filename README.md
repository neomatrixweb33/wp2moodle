wp2moodle
=========

WordPress to Moodle (wp2moodle) est un plugin qui permet aux utilisateurs de WordPress d'ouvrir des cours Moodle sans avoir de boîte de connexion entre les deux. Il inscrira également (éventuellement) l'utilisateur dans des cohortes, des cours et des groupes.

Il utilise un lien crypté et ** ne repose pas sur SSL / https ** (bien qu'il soit recommandé d'utiliser SSL dans la mesure du possible). Vos serveurs WordPress et Moodle peuvent être sur le même hôte ou sur des réseaux ou des technologies de serveur différents. Comme il n'utilise que des hyperliens pour communiquer, il n'y a pas de configuration spéciale.

Le plugin a ces limitations par conception :

- Les utilisateurs créés via ce plugin ne peuvent pas se connecter à Moodle en utilisant leur nom d'utilisateur WordPress - ils doivent se connecter à partir du lien généré par ce plugin.
- Vous ne pouvez pas faire marche arrière ; c'est-à-dire connectez-vous à Moodle et reconnectez-vous à WordPress (en utilisant ces utilisateurs - d'autres plugins d'authentification fonctionnent toujours)
- WordPress n'est informé d'aucun résultat de cours
- WordPress n'est pas informé des modifications apportées au profil utilisateur par Moodle (bien que le plugin désactive normalement le mot de passe)
- WordPress n'a aucun moyen de savoir si les valeurs liées existent dans Moodle (par exemple, il ne vérifie pas votre travail)

Les données sont cryptées (à l'aide d'aes-256-cbc via openssl) du côté de Wordpress et transmises via une requête http GET standard. Seules les informations minimales requises sont envoyées afin de créer un enregistrement d'utilisateur Moodle. L'utilisateur est automatiquement créé s'il n'est pas présent à la fin de Moodle, puis authentifié et (éventuellement) inscrit dans une cohorte, un groupe ou les deux.

Comment ça fonctionne
------------

Ce plugin vous permet de placer un shortcode dans une publication qui transmet les informations de connexion cryptées à Moodle (nécessite que ce plugin soit également installé dans Moodle). L'utilisateur sera ajouté à Moodle et éventuellement inscrit dans la ou les cohortes, cours et/ou groupes spécifiés.

Utilisez le bouton Moodle sur l'éditeur riche pour insérer le shortcode, ou entrez les détails manuellement en utilisant les exemples ci-dessous comme guide.

Example: `[wp2moodle class='css-classname' group='group1' cohort='class1' target='_blank' authtext='Please log on']launch the course[/wp2moodle]`


| Attribute | Kind | Purpose | Example |
| --- | --- | --- | --- |
| `class` | optional | defaults to 'wp2moodle'; CSS class attribute of link | `[wp2moodle course='abc1' class='wp2m-link']Open[/wp2moodle]` |
| `cohort` | optional, csv | idnumber of one or more cohorts in which to enrol a user | `[wp2moodle cohort='business_cert3']enrol in Cert 3 Business[/wp2moodle]` |
| `group` | optional, csv | idnumber of one or more groups in which to enrol | `[wp2moodle group='eng14_a,math14_b,hist13_c']Math, English & History[/wp2moodle]` |
| `course` | optional, csv | idnumber of one or more courses in which to enrol | `[wp2moodle course='abc1,abc2,def1']Enrol in 3 courses[/wp2moodle]` |
| `target` | optional | defaults to '_self'; href target attribute of link | `[wp2moodle course='abc1' target='_blank']Open[/wp2moodle]` |
| `authtext` | optional | string to display if not yet logged on, can be a shortcode | `[wp2moodle authtext='Please log on first' course='abc1']Open the course[/wp2moodle]` |
| `activity` | optional, depreciated | 1-based index (count) of visible activites | `[wp2moodle course='abc1' activity='2']Open course page[/wp2moodle]` |
| `cmid` |optional | Activity ID to open (e.g. /mod/plugin/view.php?id=XXX) | `[wp2moodle course='abc1' cmid='4683']Open course blog[/wp2moodle]` |
| `url` | optional | Url to open after logon (overrides everything else) | `/mod/customplugin/index.php?id=123` |


Conditions
------------
PHP 5.6+ (Recommandé : 7.3 ou supérieur)
Moodle 3.1 ou supérieur (Recommandé : 3.6.4 ou supérieur, dernière vérification dans 3.10.1+)
Wordpress 4 ou supérieur (Recommandé : 5.2.2 ou supérieur, dernière vérification en 5.7)
extension openssl sur votre php (vous l'avez probablement)

Comment installer ce plugin
---------------------

1. téléchargez le plugin dans un fichier zip nommé wp2moodle.zip
2. dans wordpress, choisissez `Plugins > Add New > Upload Plugin` et téléchargez et activez le plugin de la manière normale
3. dans moodle, choisissez `Administration du site > Plugins > Installer les plugins` et téléchargez et activez le plugin de la manière normale


Usage:
------
Vous ne pouvez pas utiliser ce plugin directement ; il est lancé par wp2moodle depuis Wordpress.

*IMPORTANT* : lorsque vous créez un lien vers des éléments par leur « id », assurez-vous d'utiliser le champ moodle « numéro d'identification ». Ceci est souvent vide par défaut - vous devez le définir.

Licence:
--------
GPL3, selon Moodle.
