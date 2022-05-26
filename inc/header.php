<!DOCTYPE html>
<html>
<head>
    <title><?php echo SITE_NAME ?></title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="assets/css/spectre-<?php echo WEB_THEME ?>.css"/>
    <link rel="stylesheet" href="assets/css/icons.min.css"/>
    <link rel="stylesheet" href="assets/css/common.css"/>
</head>
<button class="btn" onclick="window.location.href = 'index.php'">Accueil</button>
<body class="<?php echo WEB_THEME; ?>">
<div id="banner" class="flex flex-space">
    <?php
        if(!empty($_SESSION['username'])){
    ?>
    <div class="dropdown">
        <button class="btn btn-link dropdown-toggle" tabindex="0">connecté en tant que <?php echo $username ?>
            <i class="icon icon-caret"></i>
        </button>
        <ul class="menu">
        <li class="menu-item">
            <button class="btn" onClick="openModal('modal-change-pwd')">Changer le mot de passe</a>
        </li>
            <li class="menu-item">
            <button class="btn" onclick="window.location.href = 'login.php?logout'">Se déconnecter</button>
            </li>
        </ul>
    </div>

    <?php
        }
        else{
    ?>
        <button class="btn" onClick="openModal('modal-login')">Connexion</button>
        <?php
            if (ALLOW_SIGNIN == 'true'){
        ?>
            <button class="btn" onClick="openModal('modal-signin')">S'inscrire</button>
        <?php
            }
        }
    ?>
</div>
<script>
    function closeAllModals(){
        document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'))
    }

    function openModal(modalId){
        document.getElementById(modalId).classList.add('active')
    }
</script>
<div class="modal modal-sm" id="modal-login">
  <a class="modal-overlay" aria-label="Close"></a>
  <div class="modal-container">
    <div class="modal-header">
      <a href="#close" class="btn btn-clear float-right" aria-label="Close"></a>
      <div class="modal-title h5">Formulaire de connexion </div>
    </div>
    <div class="modal-body">
      <div class="content">
        <form action="login.php" method="POST" id="login">
        <div class="form-group">
            <label class="form-label" for="login_username">Nom d'utilisateur</label>
            <input class="form-input" type="text" id="login_username" name="username"/>
        </div>
        <div class="form-group">
            <label class="form-label" for="login_password">Mot de passe</label>
            <input class="form-input" type="password" id="login_password" name="password"/>
        </div>
        <input class="btn float-right" type="submit" value="login" />
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal modal-sm" id="modal-signin">
  <a class="modal-overlay" aria-label="Close"></a>
  <div class="modal-container">
    <div class="modal-header">
      <a href="#close" class="btn btn-clear float-right" aria-label="Close"></a>
      <div class="modal-title h5">Formulaire d'inscription</div>
    </div>
    <div class="modal-body">
      <div class="content">
        <form action="login.php?signin" method="POST" id="signin">
        <div class="form-group">
            <label class="form-label" for="register_username">Nom d'utilisateur</label>
            <input class="form-input" type="text" id="register_username" name="username"/>
        </div>
        <div class="form-group">
            <label class="form-label" for="register_password">Mot de passe</label>
            <input class="form-input" type="text" id="register_password" name="password"/>
        </div>
        <div class="form-group">
            <label class="form-label" for="register_email">Email</label>
            <input class="form-input" type="email" id="register_email" name="email"/>
        </div>
        <input class="btn float-right" type="submit" value="inscription" />
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal modal-sm" id="modal-change-pwd">
  <a class="modal-overlay" aria-label="Close"></a>
  <div class="modal-container">
    <div class="modal-header">
      <a href="#close" class="btn btn-clear float-right" aria-label="Close"></a>
      <div class="modal-title h5">Formulaire de changement de mot de passe</div>
    </div>
    <div class="modal-body">
      <div class="content">
        <form action="login.php?changepassword" method="POST" id="login">
        <div class="form-group">
            <label class="form-label" for="old_password">ancien mot de passe</label>
            <input class="form-input" type="password" id="old_password" name="old_password"/>
        </div>
                <div class="form-group">
            <label class="form-label" for="new_password">nouveau mot de passe</label>
            <input class="form-input" type="password" id="new_password" name="new_password"/>
        </div>
        <input class="btn float-right" type="submit" value="Login" />
        </form>
      </div>
    </div>
  </div>
</div>
<script>
	function closeAllModals(){
        document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'))
    }
    function openModal(modalId){
        document.getElementById(modalId).classList.add('active')
    }
	[
		...document.querySelectorAll('.modal-overlay'),
		...document.querySelectorAll('.modal .btn-clear')
	].forEach(o => o.addEventListener('click', closeAllModals))
</script>

