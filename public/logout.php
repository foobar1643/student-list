<?php

setcookie('token', null, -1);
setcookie('auth', null, -1);
header("Location: index.php");
