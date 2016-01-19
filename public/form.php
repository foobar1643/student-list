<?php

require_once("../app/AutoLoader.php");

$app = new ControllerForm();

if($_POST) $app->process_post_request($_POST);

if($_GET) $app->process_get_request($_GET);

$app->run();
