<?php
spl_autoload_register(function($className) {
    $path = __DIR__ . "/../src/" . str_replace("\\", "/", $className) . '.php';
    if (file_exists($path)) {
        require $path;
        return true;
    }
    return false;
});
