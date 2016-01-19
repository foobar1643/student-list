<?php

require_once("../app/AutoLoader.php");

$app = new ControllerFiller();

if($_POST) $app->process_post_request($_POST);

$app->run();
