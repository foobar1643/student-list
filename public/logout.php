<?php
require("../app/init.php");

$container["authHelper"]->logOut();
header("Location: index.php");
