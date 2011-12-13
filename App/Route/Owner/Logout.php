<?php
namespace App\Route\Owner;

use Eight\Route;
use Eight\Entity\Owner;
use Respect\Relational\Sql;

use \BadMethodCallException as Method;

class Logout extends Route
{
    const URL = '/logout';
    
    public function get()
    {
        $_SESSION['owner'] = null;
		header('Location: /');
    }
    
    public function post($name=null, $salt=null)
    {
        $this->get();
    }
}