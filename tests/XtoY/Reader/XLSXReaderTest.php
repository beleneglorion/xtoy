<?php

use XtoY\Reader\XLSXReader;

class XLSXReaderTest extends PHPUnit_Framework_TestCase
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
        $options = array('skip'=>1);
        $this->object = new XLSXReader($options);
        $this->object->setDSN(INPUT_DIR.'/test1.xlsx');
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
      $this->assertEquals(realpath(INPUT_DIR.'/test1.xlsx'), $dsn);
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
        $this->assertCount(6, $data);
        foreach($data as $line) {
             $this->assertCount(18, $line);
        }
        $this->assertEquals('e8c850a6a6b7e097807ea9473cf78ffd', md5(json_encode($data)));
        
    }
    
    
    
}
