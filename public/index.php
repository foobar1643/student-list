<?php

require_once("../app/AutoLoader.php");

$app = new ControllerIndex();

if($_GET) $app->process_get_request($_GET);

if($_POST) $app->process_post_request($_POST);

$app->run();
