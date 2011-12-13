<?php
namespace App\Route\Owner;

use Eight\Route;
use Eight\Entity\Owner;
use Respect\Relational\Sql;
use \StdClass;
use \BadMethodCallException as Method;

class Auth extends Route
{
    const URL = '/auth';
    
    public function get()
    {
        return array('view'=>'Login.twig');
    }
    
    public function post($_name=null, $_salt=null)
    {
        $name  = $_name ?: filter_input(INPUT_POST, 'name');
		$salt  = $_salt ?: filter_input(INPUT_POST, 'salt');
		$owner = $this->getOwner($name, $salt);
		
		// Owner found, loggin as given owner
		if ($owner) {
			$_SESSION['owner'] = serialize($owner);
			header('Location: /owner/texts');
		}
		
		// Already tried to create owner
		if (!is_null($_name) && !is_null($_salt)) 
			throw new Method('Unable to create owner');

		// Owner not found, create it
		$owner = new StdClass();
		$owner->id   = null;
		$owner->name = $name;
		$owner->salt = $salt;
		$this->app->db()->persist($owner, 'owner');
		$this->app->db()->flush();
		$this->post($name, $salt);
    }

	protected function getOwner($name, $salt)
	{
		$where = Sql::where('name = "'.$name.'"')->and('salt = "'.$salt.'"');
		$owner = $this->app->db()->owner->fetch($where);
		if ($owner)
			return Owner::createFromObject($owner);

		return $owner;
	}
}