<?php
use \reflectionClass;
use Eight\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{   
    /**
     * @expectedException UnexpectedValueException
     */
    public function testPathException()
    {
        new Application('/non/exists');
    }
    
    public function testInstance()
    {
        $app = new Application('./../App');
        $this->assertInstanceOf('Eight\Application', $app);
        return $app;
    }
    
    /**
     * @depends testInstance
     */
    public function testGetPath($app)
    {
        $reflection = new ReflectionClass($app);
        $property   = $reflection->getProperty('path');
        $property->setAccessible(true);
        $path       = $property->getValue($app);
        $this->assertEquals("{$path}/config.ini", $app->getPath('config.ini'));
    }

    /**
     * @depends testInstance
     */
    public function testGetNamespace($app)
    {
        $reflection = new ReflectionClass($app);
        $property   = $reflection->getProperty('namespace');
        $property->setAccessible(true);
        $property->setValue($app, 'App');
        
        $this->assertEquals('App\Route\Welcome', $app->getNamespace('Route\Welcome'));
    }

    /**
     * @depends testInstance
     */
    public function testParseConfiguration($app)
    {
        $class  = new ReflectionClass($app);
        $method = $class->getMethod('parseConfiguration');
        $method->setAccessible(true);
        $method->invoke($app);
        
        $this->assertAttributeInstanceOf('Respect\Config\Container', 'config', $app);
        return $app;
    }

    /**
     * @depends testParseConfiguration
     */
    public function testTwig($app)
    {
        $app->twig();
        $this->assertInstanceOf('Twig_Environment', $app->twig());
    }

    /**
     * @depends testParseConfiguration
     */
    public function testDb($app)
    {
        $app->db();
        $this->assertInstanceOf('Eight\Database\Mapper', $app->db());
    }
    
    /**
     * @depends testParseConfiguration
     */
    public function testHttpRoutes($app)
    {
        $class  = new ReflectionClass($app);
        $method = $class->getMethod('registerHttpRoutes');
        $method->setAccessible(true);
        $method->invoke($app);
        
        $this->assertAttributeInstanceOf('Respect\Rest\Router', 'http', $app);
        return $app;
    }
}
