<?php
require("../app/init.php");

use \App\Controller\ControllerForm;

$app = new ControllerForm($container);
runApp($app);