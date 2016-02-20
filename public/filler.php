<?php
require("../app/init.php");

use \App\Controller\ControllerFiller;

$app = new ControllerFiller($container);
runApp($app);