<?php

namespace XtoY\Reader;

use XtoY\Reader\ReaderInterface;
use XtoY\Options\Optionnable;

class CSVReader extends Optionnable implements ReaderInterface
{
   protected $dsn;
   protected $handler;

   public function __construct($options)
   {
       parent::__construct();

        $this->addOption('delimiter',';');
        $this->addOption('enclosure','"');
        $this->addOption('length','1024');
        $this->addOption('escape','\\');
        $this->addOption('skip','0');
        $this->getOptionManager()->init($options);
   }

   public function setDSN($dsn)
   {

      $this->dsn = $dsn;
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
        $this->handler = fopen($filename, 'r');
        if (!is_resource($this->handler)) {
             throw new \Exception(sprintf('Could not open (%s) for reading',$filename));
        }

       }

   }

   public function close()
   {
      if (isset($this->handler) && is_resource($this->handler)) {
          fclose($this->handler);
      }
   }

   public function fetch()
   {
       $options = $this->getOptionManager()->getOptions();

       return fgetcsv($this->handler,$options['length'],$options['delimiter'],$options['enclosure'],$options['escape']);

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
       $nbtoSkip = $this->getOption('skip');
       for ($i = 0; $i< $nbtoSkip;$i++) {
           $this->fetch();
       }

   }

}
