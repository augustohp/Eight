<?php
use \StdClass;
use \PHPUnit_Framework_TestCase as TestCase;
use Eight\Entity\Owner;

class EntityOwnerTest extends TestCase
{
    
    public function constructs()
    {
        return array(
            array('Test', 'testing ...'),
            array('#51', 'Fifty one'),
        );
    }
    
    /**
     * @dataProvider constructs
     */
    public function testConstructor($name, $salt)
    {
        $object = new Owner($name, $salt);
        $this->assertEquals($name, $object->name);
        $this->assertEquals($salt, $object->salt);
    }

	public function testCreationFromObject()
	{
		$id        = 2;
		$name      = 'test';
		$salt      = 'testsaltstring';
		$obj       = new StdClass;
		$obj->id   = $id;
		$obj->name = $name;
		$obj->salt = $salt;
		$owner     = Owner::createFromObject($obj);
		$this->assertAttributeEquals($id, 'id', $owner);
		$this->assertAttributeEquals($name, 'name', $owner);
		$this->assertAttributeEquals($salt, 'salt', $owner);
	}
}