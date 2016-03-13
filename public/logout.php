<?php
require("../app/init.php");

$helper = $container["authHelper"];
$helper->logOut();
header("Location: index.php");
