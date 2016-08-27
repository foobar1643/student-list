<?php
require("../src/init.php");

use App\Controller\ControllerForm;

$app = new ControllerForm($container);
$app();