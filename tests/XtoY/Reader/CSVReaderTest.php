<?php

use XtoY\Reader\CSVReader;

class CSVReaderTest extends PHPUnit_Framework_TestCase
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
        $this->object = new CSVReader($options);
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
      $this->object->setDSN(INPUT_DIR.'/test1.csv');
      $dsn =  $this->object->getDSN();
      $this->assertEquals(realpath(INPUT_DIR.'/test1.csv'), $dsn);
    }
    
    /**
     * @depends testDsn
     */
    public function testFetchAll()
    {
        $this->object->setDSN(INPUT_DIR.'/test1.csv');
        $this->object->open();
        $this->object->preprocessing();
        $data = $this->object->fetchAll();
        $this->object->close();
        $this->assertInternalType("array", $data);
        $this->assertCount(4, $data);
        foreach($data as $line) {
             $this->assertCount(4, $line);
        }
        $this->assertEquals('43c4b0ff554c05dffd7460fe52224d31', md5(json_encode($data)));
        
    }
    
    
    
}
