<?php

spl_autoload_register(function ($class) {
    $path = "../app/" . $class . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

spl_autoload_register(function ($class) {
    $path = "../app/Controller/" . $class . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

spl_autoload_register(function ($class) {
    $path = "../app/Model/" . $class . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

spl_autoload_register(function ($class) {
    $path = "../app/View/" . $class . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});
