<?php

use XtoY\Reader\PDOReader;

class PDOReaderTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * @var PDOReader
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $options = array('query'=>'SELECT * FROM product');
        $this->object = new PDOReader($options);
        $this->object->setDSN('sqlite:'.INPUT_DIR.'/test1.sqlite');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
    
    public function testDsn()
    {
      
      $dsn =  $this->object->getDSN();
      $this->assertEquals('sqlite:'.INPUT_DIR.'/test1.sqlite', $dsn);
    }
    
    /**
     * @depends testDsn
     */
    public function testFetchAll()
    {

        $this->object->open();
        $this->object->preprocessing();
        $data = $this->object->fetchAll();
        $this->object->close();
        $this->assertInternalType("array", $data);
        $this->assertCount(5, $data);
        foreach($data as $line) {
             $this->assertCount(5, $line);
        }
        $this->assertEquals('e9b80f3372883e517a8761221263d13b', md5(json_encode($data)));
        
    }
    
    
    
}
