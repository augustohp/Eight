<?php
use \PHPUnit_Framework_TestCase as TestCase;
use Eight\Privacy\Control;
use Eight\Entity\Owner;
use \StdClass;

class PrivacyControlTest extends TestCase
{
    public function owners()
    {
        $owners      = array();
        
        $owner       = new StdClass();
        $owner->id   = 1;
        $owner->name = 'Label';
        $owner->salt = 'More long salt';
        $owners[]    = $owner;
        
        $owner       = new Owner('Cool', 'Not cool at all');
        $owner->id   = 2;
        $owners[]    = $owner;
        
        return $owners;
    }
    
    public function entities()
    {
        $entities = array();
        
        $entity           = new StdClass;
        $entity->id       = 1;
        $entity->owner_id = 1;
        $entity->title    = 'Testing 123';
        $entity->content  = 'This is a buzz ...';
        $entities[]       = $entity;
        
        return $entities; 
    }
    
    public function constructs()
    {
        $return = array();
        
        foreach ($this->owners() as $owner)
            foreach ($this->entities() as $entity)
                $return[] = array($owner, $entity);

        return $return;
    }
    
    /**
     * @dataProvider constructs
     */
    public function testConstruct($owner, $entity)
    {
        $object   = new Control($owner, $entity);
        $id       = $entity->id;
        $owner_id = $entity->owner_id;
        $title    = $entity->title;
        
        $this->assertAttributeEquals($owner, 'owner', $object);
        $this->assertAttributeEquals($entity, 'entity', $object);
        
        $object->encode();
        $this->assertEquals($id, $entity->id);
        $this->assertEquals($owner_id, $entity->owner_id);
        $this->assertFalse(($title === $entity->title));
    }

	public function testSimpleEncode()
	{
		$secret  = 'This is my secret';
		$owner   = new Owner('pascutti', 'mysecretsalt');
		$entity  = new StdClass;
		$privacy = new Control($owner, $entity);
		
		$entity->secret = $secret;
		$privacy->encode();
		$this->assertFalse($entity->secret == $secret);
	}
}