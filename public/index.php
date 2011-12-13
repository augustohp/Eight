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
set_include_path(realpath('../library').PS.get_include_path());
ini_set('display_errors', 1);
error_reporting(-1);

function error_handler($code, $message, $file, $line)
{
    throw new ErrorException($message, 0, $code, $file, $line);
}
set_error_handler("error_handler");

try {
    header('Server: Apache', true);
    header('X-Powered-By: The Force', true);
	session_start();
    $app = new Eight\Application(__DIR__.'/../App');
    $app->run();
} catch (Exception $e)
{
    echo "<pre>";
    echo "Somethign very wrong happened! =( \n";
    echo $e;
}