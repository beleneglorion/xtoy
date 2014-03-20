<?php

namespace XtoY\Mapper;

use XtoY\Mapper\MapperInterface;
use XtoY\Reporter\ReporterInterface;

/**
 * Description of Mapper
 *
 * @author sebastien
 */
class Mapper implements MapperInterface
{
    protected $rules;
    protected $line=0;
    
    /**
     *
     * @var ReporterInterface 
     */
    protected $reporter;
    
    

    public function applyRule($line,$ruleConfig)
    {
        static $counter = 0;
        $returnValue = null;
        if (isset($ruleConfig['src'])) {
            if (is_array($ruleConfig['src'])) {
                $data = array();
                foreach ($ruleConfig['src'] as $field) {
                    $data[$field] = $line[$field];
                }

            } elseif ($ruleConfig['src'] === '*') {
                $data = $line;
            } else {
                $data = $line[$ruleConfig['src']];
            }
            if (isset($ruleConfig['exclude']) && is_array($ruleConfig['exclude']) && is_array($data)) {
               foreach ($ruleConfig['exclude'] as $field) {
                   unset($data[$field]);
        }
            }

            if (isset($ruleConfig['callback'])) {
               $callback  = $ruleConfig['callback'];
               if (is_callable($callback)) {
                 $returnValue= call_user_func($callback, $data);
               }
            } else {
               $returnValue = $data;
            }
        }
         if (isset($ruleConfig['value'])) {
            $returnValue =$ruleConfig['value'];
         }
         if (isset($ruleConfig['counter'])) {
            $returnValue =$counter++;;
         }

        return $returnValue;
    }

    public function convert($line)
    {
        $returnValue = array();
        $rules = $this->getRules();
        foreach ($rules as $outputField=>$ruleConfig) {
            $returnValue[$outputField] = $this->applyRule($line,$ruleConfig);
        }
        if($this->reporter) {
            $this->reporter->setMappedLines(++$this->line);
        }
        return $returnValue;
    }

    public function batchConvert($datas)
    {
        $returnValue = array();
        $rules = $this->getRules();
        foreach ($datas as $idx=>$line) {
            $returnValue[$idx] = array();
            foreach ($rules as $outputField=>$ruleConfig) {
                  $returnValue[$idx][$outputField] = $this->applyRule($line,$ruleConfig);
            }
            if($this->reporter) {
            $this->reporter->setMappedLines(++$this->line);
            }
        }

        return $returnValue;
    }

    public function getRules()
    {
        return $this->rules;

    }

    public function setRules($rules)
    {
        $this->rules = $rules;

        return $this;
    }
    
   public function setReporter(ReporterInterface $reporter) {
       
       $this->reporter = $reporter;
       
       return $this;
   }

}
