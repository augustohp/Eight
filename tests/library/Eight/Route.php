<?php
use Eight\Application;
use Eight\Route;
use App\Route\Welcome;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorEmpty()
    {
        $object = new Welcome();
        $this->assertInstanceOf('Eight\Route', $object);
    }
    
    /**
     * @depends testConstructorEmpty
     */
    public function testConstruct()
    {
        $mock   = $this->getMockBuilder('Eight\Application')
                       ->disableOriginalConstructor()
                       ->getMock();
        $object = new Welcome($mock);
        $this->assertInstanceOf('Eight\Application', $mock);
        $this->assertAttributeEquals($mock, 'app', $object);
    }
}