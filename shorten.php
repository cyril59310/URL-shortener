<?php

include 'inc/bdd.php';
session_start(['cookie_lifetime' => '1728000', 'name' => 'shortener', 'cookie_httponly' => true,'cookie_secure' => true]);

if(!empty($_SESSION['username'])){
    $username = $_SESSION['username'];
    $token = $_SESSION['token'];
}
if (PUBLIC_INSTANCE == 'true' and empty($username)){
    $username = 'UNKNOWN';
}

function short($connexion, $username, $url, $custom, $comment) {
    if (preg_match("_(^|[\s.:;?\-\]<\(])(https?://[-\w;/?:@&=+$\|\_.!~*\|'()\[\]%#,?]+[\w/#](\(\))?)(?=$|[\s',\|\(\).:;?\-\[\]>\)])_i", $url)) {
        $unic = 0;
        
        if (!empty($custom)) {
            $verify_url = $connexion->prepare("SELECT * FROM shortener WHERE short=?");
            $verify_url->execute(array($custom));
            
            if (count($verify_url->fetchAll((PDO::FETCH_ASSOC))) == 0) {
                $unic = 1;
                $url_shortened = $custom;
            }
        }
        
        while ($unic == 0) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-*_+';
            $url_shortened = '';
            for ($i = 0; $i < URL_SIZE; $i++) { 
                $url_shortened .= $characters[rand(0, strlen($characters) - 1)];
            }

            $verify_url = $connexion->prepare("SELECT * FROM shortener WHERE short=?");
            $verify_url->execute(array($url_shortened));
            
            if (count($verify_url->fetchAll((PDO::FETCH_ASSOC))) == 0) {
                $unic = 1;
            }
        }
        
        $req = $connexion->prepare('INSERT INTO shortener(short,url,comment,username,date,views) VALUES (?,?,?,?,?,?)');
        $req->execute(array($url_shortened, $url, $comment, $username, date("Y-m-d H:i:s"), '0'));

        $req->closeCursor();
        return $url_shortened;
    }
    else {
        echo 'Wrong URL';
        exit;
    }
}


if (!empty($_GET['url'])) {
    $url = $_GET['url'];
    
    if (empty($_GET['comment'])) {
        $comment = NULL;
    }
    else {
        $comment = $_GET['comment'];
    }

    if (!empty($_GET['token']) and (PUBLIC_INSTANCE != 'true')) { 
        $req = $connexion->prepare('SELECT * FROM users where token = ?');
        $req->execute(array($_GET['token']));
        $res_user = $req->fetch(PDO::FETCH_ASSOC);
        if ($res_user and !empty($_GET['url'])) { 
           $url_shortened = short($connexion, $res_user['username'], $url, NULL, $comment);
        }
    }
    elseif (PUBLIC_INSTANCE == 'true') {
        $url_shortened = short($connexion, $username, $url, NULL, $comment);
    }
    else {
        header('Location: ' . DEFAULT_URL);
    }
}
else {
    if (empty($username)) { 
        if (!empty($_POST['is_short_free'])) {
            http_response_code(403); 
        }
        else {
            header('Location: ' . DEFAULT_URL);
        }
        exit();
    }
    if (!empty($_POST['is_short_free'])) {
        $verify_url = $connexion->prepare("SELECT * FROM shortener WHERE short=?");
        $verify_url->execute(array($_POST['is_short_free']));
        header('Content-type: application/json');
        if (count($verify_url->fetchAll((PDO::FETCH_ASSOC))) == 0) {
            echo json_encode(array('ok'=>true));
            exit;
        }
        else {
            echo json_encode(array('ok'=>false));
            exit;
        }
    }

    if (empty($_POST['url'])) {
        include 'inc/header.php';
        echo '
        <div id="content">
            <div id="site">
                <a href=".">'. SITE_NAME .'</a>
            </div>
            <div id="shortened">
            <b>Un problème est survenu...</b>  <br /><p> Vous devez forunir une URL !</p>
            </div>
        </div>';
        include 'inc/footer.php';
            exit();
    }

    $url = htmlspecialchars(strip_tags($_POST['url']));
    $comment = (!empty($_POST['comment'])) ? htmlspecialchars(strip_tags($_POST['comment'])) : NULL;
    $custom = (!empty($_POST['custom'])) ? htmlspecialchars(strip_tags($_POST['custom'])) : NULL;
    $url_shortened = short($connexion, $username, $url, $custom, $comment);
}
include 'inc/header.php';
echo '
    <div id="content">
        <div id="site">
            <a href=".">'. SITE_NAME .'</a>
        </div>

        <div id="shortened">
        <b>URL raccourci :</b> <br /><a id="newURL" href="' . DEFAULT_URL . '/' . $url_shortened . '">' . DEFAULT_URL . '/' . $url_shortened . '</a>
        </div>
    </div>';
include 'inc/footer.php';
?>
