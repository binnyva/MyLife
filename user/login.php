<?php
include("../common.php");

if(isset($_REQUEST['action']) and $_REQUEST['action'] == 'Login') {
	if($user->login($QUERY['username'], $QUERY['password'], $QUERY['remember'])) {
		//Successful login.
		iframe\App::showMessage("Welcome back, $_SESSION[user_name]", "index.php", "success");
	}
}

render();
