<?php
include("inc/bdd.php");

if (isset($_GET['site']) && !empty($_GET['site'])) { 

    $site = $_GET['site'];

    $get_site = $connexion->prepare('SELECT url, short, views FROM shortener WHERE short=?');
    $get_site->execute(array($site));
    $res_site = $get_site->fetch(PDO::FETCH_ASSOC);

    if ($res_site)
    {
        $views_plus_1 = $res_site['views'] + 1;

        $query_update = $connexion->prepare('UPDATE shortener SET views=? WHERE short=?');
        $query_update->execute(array($views_plus_1, $res_site['short']));

        header('Location: ' . $res_site['url']);
    } else {
        include 'inc/header.php';
        echo '
    <div id="content">
        <div id="site">
            <a href=".">'. SITE_NAME .'</a>
        </div>
        <div id="shortened">
            <b>Un problème est survenu...</b>  <br /><p> l\'url demandée n\'existe pas ou a était supprimer</p>
        </div>
    </div>';
        include 'inc/footer.php';
    }
    exit();
}

session_start(['cookie_lifetime' => '1728000', 'name' => 'shortener', 'cookie_httponly' => true, 'cookie_secure' => true]);
if(!empty($_SESSION['username'])){
    $username = $_SESSION['username'];
    $token = $_SESSION['token'];
}
if (PUBLIC_INSTANCE == 'true' and empty($username)){
    $username = 'UNKNOWN';
    $token = '';
}
if (PUBLIC_INSTANCE != 'true' and empty($username)) {
    $token = '';
}

include 'inc/header.php';

$code_js = 'javascript:(function () {var d = document;var w = window;var enc = encodeURIComponent;var f =\' ' . DEFAULT_URL . '\';var l = d.location;var p = \'/shorten.php?url=\' + enc(l.href) + \'&amp;comment=\' + enc(d.title) + \'&amp;token=' . $token . '\';var u = f + p;var a = function () {if (!w.open(u))l.href = u;};if (/Firefox/.test(navigator.userAgent))setTimeout(a, 0); else a();void(0);})()';

?>
    <script>
        async function checkCustomUrlAvailable(e){
            const {value} = e.target
            if(value === ""){
                return disableCustomUrlField(false)
            }
            const result = await fetch('/shorten.php', {method: "POST", headers: {"Content-Type": "application/x-www-form-urlencoded"}, body: `is_short_free=${value}`})
            if(!result.ok){
                return disableCustomUrlField(true)
            }
            try{
                const {ok} = await result.json()
                disableCustomUrlField(!ok)
            }
            catch(e){
                console.error(e)
                return disableCustomUrlField(true)
            }
        }

        function disableCustomUrlField(isDisabled){
            const i = document.querySelector('input[name=custom]')
            i.setCustomValidity(isDisabled ? "Cette URL personnalisée existe déjà." : '')
        }
    </script>
    <div id="content">
        <form name="url_form" action="shorten.php" method="post">
            <div class="form-group">
                <label class="form-label" for="shorten_form_url">Lien a raccourcir</label>
                <input class="form-input" type="text" id="shorten_form_url" name="url"/>
            </div>
            <div class="flex flex-space">
                
                </div>
            <div>
                <input type="submit" value="Raccourcir" class="btn btn-primary"/>
                <button class="btn float-right" onclick="event.preventDefault(); window.location.href = '/list.php'">Liste des liens raccourcis</button>
            </div>
        </form>
        <div class="flex">
            </div>
        </div>
    </div>
<?php include 'inc/footer.php';?>
