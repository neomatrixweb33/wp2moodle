<?php require_once(ABSPATH . 'wp-includes/pluggable.php'); ?>
<style>
.wp2m-table {width:100%;background-color:#fff;}
.wp2m-table td, .wp2m-table th { padding: 10px; vertical-align: top; align: left;}
.wp2m-table code {line-height:1.5;background-color:transparent;}
.wp2m-error {border-left:4px solid #c00;background-color:#fff;padding:10px;box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);}
</style>
<div class="wrap">


<?php if (!extension_loaded('openssl')) { ?>
    <div class="wp2m-error">
    <h2>Avertissement!</h2><p>le module <em>openssl</em> L'extension php n'a pas été détectée. Vous devrez corriger votre configuration PHP avant que ce plugin ne fonctionne.</p>
    </div>
<?php } ?>

<h2><?php print WP2M_PUGIN_NAME ." ". WP2M_CURRENT_VERSION ?></h2>
<p>Ce plugin vous permet de placer un shortcode dans une publication qui transmet les informations de connexion cryptées à Moodle (nécessite que ce plugin soit également installé dans Moodle). L'utilisateur sera ajouté à Moodle et éventuellement inscrit dans la ou les cohortes, cours et/ou groupes spécifiés.</p>
<p>Utilisez le bouton Moodle sur l'éditeur riche pour insérer le shortcode, ou entrez les détails manuellement en utilisant les exemples ci-dessous comme guide.</p>
<p>Example: <code>[wp2moodle class='css-classname' group='group1' cohort='class1' target='_blank' authtext='Please log on']launch the course[/wp2moodle]</code>.</p>
<table class="wp2m-table">
    <thead><tr><th>Attribut</th><th>Cible</th><th>Example</th></tr></thead>
    <tbody>
    <tr><td><code>class</code></td><td> facultatif, la valeur par défaut est « wp2moodle » ; Attribut de classe CSS du lien</td></tr>
    <tr><td><code>cohort</code></td><td> facultatif, numéro d'identification de la cohorte dans laquelle inscrire un utilisateur à la fin de moodle. Vous pouvez spécifier plusieurs valeurs à l'aide de chaînes séparées par des virgules.</td><td><code>[wp2moodle cohort='business_cert3']s'inscrire au Cert 3 Business[/wp2moodle]</code></td></tr>
    <tr><td><code>group</code></td><td> facultatif, numéro d'identification du groupe dans lequel inscrire un utilisateur à la fin de moodle (généralement, vous utilisez le groupe <i>OU</i> cohort). Vous pouvez spécifier plusieurs valeurs à l'aide de chaînes séparées par des virgules.</td><td><code>[wp2moodle group='eng14_a,math14_b,hist13_c']Math, Anglais & Histoire[/wp2moodle]</code></td></tr>
    <tr><td><code>course</code></td><td> facultatif, numéro d'identification du cours auquel inscrire un utilisateur à la fin de moodle. Vous pouvez utiliser plusieurs identifiants</td><td>(comme ci-dessus)</td></tr>
    <tr><td><code>target</code></td><td> facultatif, la valeur par défaut est '_self' ; href attribut cible du lien</td><td><code>target="_blank"</code></td></tr>
    <tr><td><code>authtext</code></td><td> facultatif, par défaut le contenu entre les balises de shortcode ; chaîne à afficher si pas encore connecté</td></tr>
    <tr><td><code>activity</code></td><td> facultatif, index numérique de la première activité à ouvrir (> 0) si l'ouverture automatique est activée dans le plugin Moodle</td></tr>
    <tr><td><code>cmid</code></td><td>facultatif, identifiant numérique de la page d'affichage de l'activité (e.g. /mod/plugin/view.php?id=XXX)</td></tr>
    <tr><td><code>url</code></td><td> facultatif, remplace les autres recherches, URL moodle relative à ouvrir après la connexion (e.g. /mod/page/index.php?id=123)</td></tr>
    </tbody>
</table>
<p class="description">Le <em>idnumber</em> mentionné ci-dessus n'est pas le même que l'identifiant du cours (qui est un nombre) ; moodle a un champ spécial appelé "idnumber" qui est une valeur alphanumérique. Si vous les mélangez, cela ne fonctionnera pas !</p>
<p class="description">Le lien généré est horodaté et expirera, il ne peut donc pas être mis en signet ou piraté. Vous devez définir l'heure d'expiration dans le plugin Moodle. Vous devez autoriser le temps de lecture de la page lorsque vous envisagez une valeur de délai d'attente, car le lien est généré lorsque la page est chargée, et non lorsque le lien est cliqué. </p>

<?php if (class_exists('WooCommerce') || class_exists('MarketPress')) { ?>
<h3>MarketPress or WooCommerce?</h3>
<p>Vendre avec MarketPress ou WooCommerce ? Créez un fichier texte appelé "yourcourse-wp2moodle.txt" (en fait, le nom n'a qu'à <strong>terminer par</strong> <code>-wp2moodle.txt</code>; le nom avant cela peut être ce que vous voulez) et écrivez tous les attributs (indiqués ci-dessus) sur leurs propres lignes, comme ceci :</p>
<table class="wp2m-table">
    <tr><td>
        <code>group=maths102_sem2</code><br>
        <code>cohort=2015allCourses</code>
    </td></tr>
</table>
<?php } ?>
<p>Téléchargez ce fichier en tant que téléchargement numérique pour le produit. Ensuite, après un achat au lieu d'un téléchargement, ils seront redirigés vers votre site Moodle avec un jeton d'authentification tout comme un lien de shortcode.</p>

<h2>Settings</h2>
<form method="post" action="options.php">
    <?php
        settings_fields( 'wp2m-settings-group' );
    ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">URL racine de Moodle</th>
            <td><input type="text" name="wp2m_moodle_url" value="<?php echo get_option('wp2m_moodle_url'); ?>" size="60" />
            <div class="description">Le plugin ajoutera l'url <em style="text-decoration:underline"><?php echo WP2M_MOODLE_PLUGIN_URL ?></em>.</div>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">Encryption secret<br><span style='font-weight:normal'>Doit correspondre à Moodle</span></th>
            <td><input type="text" name="wp2m_shared_secret" value="<?php echo get_option('wp2m_shared_secret'); ?>" size="60" />
            <div class="description">Voici une clé sécurisée fraîchement générée : <code><?php echo base64_encode(openssl_random_pseudo_bytes(32)); ?></code>.</div>
            </td>
        </tr>

    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
    <p><a href="https://icons8.com/web-app/38662/Moodle" style="float: right;" target="_blank"><img src="<?php echo plugin_dir_url(__FILE__).'icon.svg'; ?>" style="vertical-align: bottom;">Moodle</a>
</div>
