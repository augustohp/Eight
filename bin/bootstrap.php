<?php
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);

/**
 * Autoloader that implements the PSR-0 spec for interoperability between
 * PHP software.
 * 
 * @author Alexandre Gaigalas <alexandre@gaigalas.net>
 */
spl_autoload_register(
    function($className) {
        $fileParts = explode('\\', ltrim($className, '\\'));
        $file      = implode(DS, $fileParts) . '.php';
        
        foreach (explode(PS, get_include_path()) as $path) {
            if (file_exists($path = $path . DS . $file))
                return require $path;
        }
    }
);

date_default_timezone_set('UTC');
set_include_path(realpath(__DIR__.'/../library').PS.get_include_path());