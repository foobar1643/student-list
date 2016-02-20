<?php
require("../app/init.php");

use \App\Controller\ControllerIndex;

$app = new ControllerIndex($container);
runApp($app);