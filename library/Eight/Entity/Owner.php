<?php
namespace Eight\Entity;

use Eight\Privacy\Control;

class Owner
{   
    public $id;
    public $name;
    public $salt;
    
    public function __construct($name='', $salt='')
    {
        $this->name = $name;
        $this->salt = $salt;
    }

	public static function createFromObject($object)
	{
		$name  = $object->name ?: null ;
		$salt  = $object->salt ?: null ;
		$id    = $object->id   ?: 0 ;
		$owner = new self($name, $salt);
		$owner->id = $id;
		return $owner;
	}
}