<?php

use XtoY\Mapper\Mapper;

class MapperTest extends PHPUnit_Framework_TestCase
{
    
    public function testValue()
    {
      $rules = array(
          'field'=> array('value'=>5)
      );
      
      $mapper = new Mapper();
      $mapper->setRules($rules);
      $output = $mapper->convert(array());
      $this->assertCount(1, $output);
      $this->assertEquals(array('field'=>5), $output);
      
    }
    
    
    public function testCounter()
    {
      $rules = array(
          'field'=> array('counter'=>1)
      );
      
      $mapper = new Mapper();
      $mapper->setRules($rules);
      for($i = 0;$i<=5; $i++) {
        $output = $mapper->convert(array());
        $this->assertCount(1, $output);
        $this->assertEquals(array('field'=>$i), $output);
      }
      
    }
    
       public function testSrc()
    {
      $rules = array(
          'field'=> array('src'=>'source_field')
      );
      
      $data = array(
          array('source_field'=>3,'unused_field'=>'nothing','nokey'),
          array('source_field'=>4,'unused_field'=>'nothing','nokey'),
          array('source_field'=>5,'unused_field'=>'nothing','nokey')
      );
      $mapper = new Mapper();
      $mapper->setRules($rules);
      $output = $mapper->batchConvert($data);
      $this->assertCount(3, $output);
      $this->assertEquals(array(
          array('field'=>3),
          array('field'=>4),
          array('field'=>5)
      ), $output);
      
    }
    
  public function testSrcCallback()
    {
      $rules = array(
          'field'=> array('src'=>'source_field','callback'=>'strtoupper')
      );
      
      $data = array(
          array('source_field'=>'a','unused_field'=>'nothing','nokey'),
          array('source_field'=>'b','unused_field'=>'nothing','nokey'),
          array('source_field'=>'c','unused_field'=>'nothing','nokey')
      );
      $mapper = new Mapper();
      $mapper->setRules($rules);
      $output = $mapper->batchConvert($data);
      $this->assertCount(3, $output);
      $this->assertEquals(array(
          array('field'=>'A'),
          array('field'=>'B'),
          array('field'=>'C')
      ), $output);
      
    }
    
    public function testSrcWildcard()
    {
      $rules = array(
          'field'=> array('src'=>'*','callback'=>'count')
      );
      
      $data = array(
          array('source_field'=>'a','unused_field'=>'nothing','nokey'),
          array('source_field'=>'b','nokey'),
          array('source_field'=>'c')
      );
      $mapper = new Mapper();
      $mapper->setRules($rules);
      $output = $mapper->batchConvert($data);
      $this->assertCount(3, $output);
      $this->assertEquals(array(
          array('field'=>3),
          array('field'=>2),
          array('field'=>1)
      ), $output);
      
    }
    
    public function testSrcExclude()
    {
      $rules = array(
          'field'=> array('src'=>'*','callback'=>'count','exclude'=>array('remove_this'))
      );
      
      $data = array(
          array('source_field'=>'a','unused_field'=>'nothing','nokey'),
          array('source_field'=>'b','nokey','remove_this'=>'nothing'),
          array('source_field'=>'c')
      );
      $mapper = new Mapper();
      $mapper->setRules($rules);
      $output = $mapper->batchConvert($data);
      $this->assertCount(3, $output);
      $this->assertEquals(array(
          array('field'=>3),
          array('field'=>2),
          array('field'=>1)
      ), $output);
      
    }
    
    
    
    
    
}
