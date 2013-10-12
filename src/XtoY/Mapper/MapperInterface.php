<?php

namespace XtoY\Mapper;


/**
 * Description of MapperInterface
 *
 * @author sebastien
 */
interface MapperInterface {
   
    public function convert($line);
    
    public function batchConvert($table);
    
    
}

