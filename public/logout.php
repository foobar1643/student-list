<?php
require("../app/init.php");

use \App\Helper\TokenHelper;

$helper = new TokenHelper();

$helper->unsetAuthToken();
header("Location: index.php");
