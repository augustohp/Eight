<?php
require 'bootstrap.php';

$app     = new Eight\Application(__DIR__.'/../App');
$pdo     = $app->parseConfiguration()->db()->getConnection();
if (!$pdo instanceof Pdo)
	throw new Exception('No database connection');

$updater = new Eight\Database\Updater($pdo);
$updater->sync();
echo "Current database version: ".$updater->getCurrentVersion(), "\n";