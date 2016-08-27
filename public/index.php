<?php
require("../src/init.php");

use App\Controller\ControllerIndex;

$app = new ControllerIndex($container);
$app();