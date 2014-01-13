<?php

namespace XtoY\Reader;

use XtoY\Reader\ReaderInterface;
use XtoY\Options\Optionnable;

class XLSXReader extends Optionnable implements ReaderInterface
{
   protected $dsn;
   protected $handler;
   protected $keys;

   public function __construct($options)
   {
       parent::__construct();

        $this->addOption('skip','0');
        $this->addOption('firstline_as_keys',false);
        $this->addOption('worksheet','');
        $this->addOption('options',array('SharedStringCacheLimit'=>50000));
        $this->getOptionManager()->init($options);
   }

   public function setDSN($dsn)
   {

      $this->dsn = $dsn;;
   }

   public function getDSN()
   {
       return $this->dsn;
   }

   public function open()
   {
       if (!isset($this->handler) || !is_resource($this->handler)) {
        $filename = $this->getDSN();
        if (!file_exists($filename)) {
            throw new \Exception(sprintf('File not exist (%s)',$filename));
        }
         if (!is_readable($filename)) {
            throw new \Exception(sprintf('File is not readable(%s)',$filename));
        }
        $this->handler = new \SpreadsheetReader($filename,false,false,$this->getOption('options'));
        // changing worksheet if needed
        $sheet = $this->getOption('worksheet');
        if (!empty($sheet)) {
            if (!is_numeric($sheet)) {
                $index =  $this->getSheetIndexForName($sheet);
                // the rewing  is done only if index > 0 then move next before
                $this->handler->next();
                $this->handler->ChangeSheet($index);
            } else {
                 $this->handler->next();
                $this->handler->ChangeSheet($sheet);
            }
        }
       }
   }

   public function getSheetIndexForName($sheetname)
   {
       $returnValue = 1;
       $sheets = $this->handler->sheets();
       foreach ($sheets as $idx=>$name) {
           if ($name == $sheetname) {
               $returnValue = $idx;
               break;
           }

       }

       return $returnValue;
   }

   public function close()
   {
      if (isset($this->handler)) {
          unset($this->handler);
      }
   }

   public function fetch($raw = false)
   {
       $options = $this->getOptions();
       $returnValue = false;
       if ($this->handler->valid()) {
            $returnValue = $this->handler->current();
            $this->handler->next();
            if ($options['firstline_as_keys'] && !$raw) {
            $nbValue = count($returnValue);
            $nbKeys =  count($this->keys);
            if($nbValue < $nbKeys) {
              for($i = $nbValue; $i < $nbKeys; $i++) {
                 $returnValue[$i] = "";
             }
            } elseif ($nbValue > $nbKeys) {
              throw new \Exception('Missing column names');
            }
           $returnValue =  array_combine($this->keys,$returnValue);
           }
       }
       
     
       return $returnValue;;

   }

   public function fetchAll()
   {
      $returnValue = array();
      do {
          $data = $this->fetch();
          if (is_null($data)) {
              $data = false;
          }
          if ($data == array(null)) {
              $data = false;
          }
          if ($data) {
              $returnValue[] = $data;
          }

      } while ($data);

      return $returnValue;

   }

   public function preprocessing()
   {
       $options = $this->getOptions();
 
       if ($options['firstline_as_keys']) {
           $this->keys =   $this->fetch(true);
       }
       $nbtoSkip =$options['skip'];
       for ($i = 0; $i< $nbtoSkip;$i++) {
           $this->fetch();
       }

   }

}
