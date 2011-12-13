<?php
namespace App\Route\Database;

use Eight\Route;
use Eight\Database\Updater;

class Update extends Route
{
    const URL = '/database/update';
    
    public function get()
    {
        $pdo     = $this->app->db()->getConnection();
        $updater = new Updater($pdo);
        $updater->sync();
        echo "Current database version: ".$updater->getCurrentVersion();
    }
}