<?php

namespace XtoY\Builder;

use XtoY\XtoY;
/**
 * Description of Builder
 *
 * @author sebastien
 */
abstract class Builder {

    
     protected static  function build($config)
     {
         
        $dsn = $config['Reader']['dsn'];
        $ddn = $config['Writer']['ddn'];
        $reader = new $config['Reader']['class']($config['Reader']['options']);
        $reader->setDsn($dsn);
        $mapper = new $config['Mapper']['class']();
        $mapper->setRules($config['Mapper']['rules']);
        $writer = new $config['Writer']['class']($config['Writer']['options']);
        $writer->setDDN($ddn);

        $uc = new XtoY();
        if(isset($config['XtoY'])) {
           if(isset($config['XtoY']['mode'])) {
               $uc->setMode($config['XtoY']['mode']);
           }
        }
        $uc->setMapper($mapper);
        $uc->setReader($reader);
        $uc->setWriter($writer);

        return $uc;
 
     }
     
     
    
    
}


