<?php
spl_autoload_register(function($className) {
    $path = __DIR__ . "/../" . str_replace("\\", "/", $className) . '.php';
    if (file_exists($path)) {
        /** @noinspection PhpIncludeInspection */
        require $path;
        return true;
    }
    return false;
});