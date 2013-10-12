<?php

namespace XtoY\Mapper;

use XtoY\Mapper\MapperInterface;

/**
 * Description of Mapper
 *
 * @author sebastien
 */
class Mapper implements MapperInterface {
   
    protected $rules;
    
    
    public function applyRule($line,$ruleConfig) {
        
        $returnValue = null;
        $field = $line[$ruleConfig['src']];
        
        if(isset($ruleConfig['callback'])) {
          $callback  = $ruleConfig['callback'];
          if(is_callable($callback)) {
              call_user_func($callback, $field);
          }
        } else {
           $returnValue =$field;
        }
        
        return $returnValue;
    }
    
    public function convert($line) {
        
        $returnValue = array();
        $rules = $this->getRules();
        foreach($rules as $outputField=>$ruleConfig) {
            $returnValue[$outputField] = $this->applyRule($line,$ruleConfig);
        }
        
        return $returnValue;
    }
    
    public function batchConvert($table) {
        
        
    }
    
    public function getRules()
    {
        return $this->rules;
        
    }
    
    public function setRules($rules) {
        
        $this->rules = $rules;
        
        return $this;
    }
    
    
}

