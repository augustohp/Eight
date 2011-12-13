<?php
use \StdClass;
use Eight\Entity\Owner;
use Eight\Entity\Text;

class EntityTextTest extends \PHPUnit_Framework_TestCase
{
    public function constructs()
    {
        $id           = 1;
        
        $owner        = new Owner();
        $owner->id    = $id;
        
        $stdClass     = new StdClass();
        $stdClass->id = $id;
        return array(
            array($id, $id),
            array($stdClass, $id),
            array($owner, $id),
            array(null,null)
        );
    }
    
    /**
     * @dataProvider constructs
     */
    public function testConstructor($owner, $owner_id)
    {
        $object = new Text($owner);
        $this->assertEquals($owner_id, $object->owner_id);
    }
}