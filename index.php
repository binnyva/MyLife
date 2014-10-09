<?php
require("./common.php");

$entries = $sql->getAll("SELECT id,body,`date` FROM Entry WHERE user_id=$_SESSION[user_id] ORDER BY `date` DESC LIMIT 0,10");

render();