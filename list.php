<?php
session_start(['cookie_lifetime' => '1728000', 'name' => 'shortener', 'cookie_httponly' => true,'cookie_secure' => true]);
if (!empty($_SESSION['username'])) {
    $username = $_SESSION['username'];
}
if (isset($_GET['UNKNOWN']) and ($_SESSION['admin'] == '1')) {
    $username = 'UNKNOWN';
}
include 'inc/bdd.php';
header("Cache-Control: no-cache, must-revalidate");
$root_url = $_SERVER['REQUEST_URI'];

if (empty($_SESSION['username'])) {
    include 'inc/header.php';
    echo '
    <div id="content">
        <div id="site">
            <a href=".">'. SITE_NAME .'</a>
        </div>
        <div id="shortened">
        <b>Un problème est survenu...</b>  <br /><p> Vous devez etre connecté pour voir la liste des URL enregistrer !</p>
        </div>
    </div>';
    include 'inc/footer.php';
    exit();
}
    
if (isset($_GET['delete']) && $_GET['delete'] != "") {
    $req = $connexion->prepare('DELETE FROM shortener WHERE username= ? AND short = ?');
    $req->execute(array($username, $_GET['delete']));
    $req->closeCursor();
}
elseif (!empty($_GET['deleteRange']) ){
    $date = new DateTime("UTC");
    $date->modify('-'.$_GET['deleteRange'].' day');
    $date = $date->format('Y-m-d H:i:s');
    if (!empty($_GET['keepBM']) && $_GET['keepBM'] == "true") {
        $req = $connexion->prepare('DELETE FROM shortener WHERE username= ? AND date < ? and comment is NULL');
        $req->execute(array($username, $date));
    }
    else {
        $req = $connexion->prepare('DELETE FROM shortener WHERE username= ? AND date < ?');
    $req->execute(array($username, $date));
    }
    $req->closeCursor();
}
include 'inc/header.php';
?>
<button class="btn" onclick="window.location.href = 'list.php?UNKNOWN'">lien crée sans compte</button>
    <table id="list" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Lien court</th>
                <th>Lien d'origine</th>
                <th>Vues totales</th>
                <th></th>
            </tr>
        </thead>
    <tbody>
    <?php

    $list = $connexion->prepare('SELECT * FROM shortener WHERE username= ? ORDER BY date DESC;');
    $list->execute(array($username));

    while ($row = $list->fetch(PDO::FETCH_ASSOC)) {
        $short = $row['short'];
        $views = $row['views'];
        $comment = $row['comment'];
        $url = $row['url'];

        $linkUrl = sprintf("./%s", $short);
        $deleteUrl = sprintf("list.php?%sdelete=%s", $username == 'UNKNOWN' ? "UNKNOWN&" : "", $short);
?>
    <tr>
        <td>
            <a href="<?php echo $linkUrl ?>">
                <?php echo $short; ?>
            </a>
        </td>
        <td>
            <div><?php echo $comment; ?></div>
            <a href="<?php echo $linkUrl; ?>">
                <?php echo $url; ?>
            </a>
        </td>
        <td>
            <?php echo $views; ?>
        </td>
        <td>
            <a href="<?php echo $deleteUrl; ?>" class="delete">
                <img src="/assets/img/delete-icon.png"/>
            </a>
        </td>
    </tr>
<?php
}
$list->closeCursor();
if ($username = 'UNKNOWN') {
    $action = 'list.php?UNKNOWN';
}
else {
    $action='list.php';
}
?>

    </tbody>
    </table>
    <div id="content" class="form action"> 
        <form class="form" action="<?php echo $action; ?>" method="get" id="formDelete" >
            <div class="form-group">
                <label class="form-label" for="deleteRange">Supprimer les liens antérieurs à</label>
                <div class="input-group">
                    <input class="form-input" type="number" id="deleteRange" name="deleteRange" value="30"/>
                    <span class="input-group-addon">Jours</span>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-checkbox">
                    <input type="checkbox" name="keepBM" value="true">
                    <i class="form-icon"></i>conserver l'URL avec les commentaires
                </label>
            </div>

            <input class="btn" type="submit" value="Supprimer" />
        </form>
    </div>
<?php include 'inc/footer.php';?>

