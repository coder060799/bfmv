<?php

require_once __DIR__.'/boot.php';

if (check_auth()) {
    header('Location: /');
    die;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Вход в My ServiceDesk 1.0</title>
	<link rel="stylesheet" type="text/css" href="./styles/login.css">
</head>
<body>
	<div class="registration-cssave">
    <form method = "post" action = "do_login.php">
        <h3 class="text-center">Вход в My ServiceDesk</h3>
         <?php flash() ?>
        <div class="form-group">
            <input class="form-control item" type="text" name="username" maxlength="15" minlength="4" pattern="^[a-zA-Z0-9_.-]*$" id="username" placeholder="Логин" required>
        </div>
        <div class="form-group">
            <input class="form-control item" type="password" name="Пароль" minlength="6" id="password" placeholder="Пароль" required>
        </div>
        <div class="form-group">
            <input class="form-control item" type="email" name="email" id="email" placeholder="Email" required>
        </div>
        <div class="form-group">
            <button class="btn btn-primary btn-block create-account" type="submit" id="vhod">Войти</button>
        </div>
    </form>
</div>
</body>
</html>