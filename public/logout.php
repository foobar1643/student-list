<?php
require("../src/init.php");

$container["authHelper"]->logOut();
header("Location: index.php");
