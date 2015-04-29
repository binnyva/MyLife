<?php
include("../common.php");
$user = new User;
$user->logout();

showMessage("User logged out.", "user/login.php");
