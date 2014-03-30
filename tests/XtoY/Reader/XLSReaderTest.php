<?php

use XtoY\Reader\XLSReader;

class XLSReaderTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * @var XtoY
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $options = array();
        $this->object = new XLSReader($options);
        $this->object->setDSN(INPUT_DIR.'/test1.xls');
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
      $this->assertEquals(realpath(INPUT_DIR.'/test1.xls'), $dsn);
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
        $this->assertCount(29, $data);
        foreach($data as $line) {
             $this->assertCount(4, $line);
        }
        $this->assertEquals('7ff20b05583e6cdc3ce645bcde8e77a3', md5(json_encode($data)));
        
    }
    
    
    
}
