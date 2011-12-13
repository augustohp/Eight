<?php
class Version extends \PHPUnit_Framework_TestCase
{
    public function testApi()
    {
        $this->assertEquals('0.1.0', Eight\Version::API);
    }
    
    public function testDatabase()
    {
        $this->assertEquals('0.1.0', Eight\Version::DATABASE);
    }
}