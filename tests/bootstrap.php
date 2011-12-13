<?php
date_default_timezone_set('UTC');
set_include_path('../' . PATH_SEPARATOR . get_include_path());
set_include_path('../library' . PATH_SEPARATOR . get_include_path());
ini_set('display_errors', 1);
error_reporting(-1);

/**
 * Autoloader that implements the PSR-0 spec for interoperability between
 * PHP software.
 */
spl_autoload_register(
    function($className) {
        $fileParts = explode('\\', ltrim($className, '\\'));
        $file      = implode(DIRECTORY_SEPARATOR, $fileParts) . '.php';
        
        foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
            if (file_exists($path = $path . DIRECTORY_SEPARATOR . $file))
                return require $path;
        }
    }
);