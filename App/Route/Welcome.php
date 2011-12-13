<?php
namespace App\Route;

use Eight\Route;
class Welcome extends Route
{
    const URL = '/';
    
    public function get()
    {
        return array('view'=>'Login.twig');
    }
}