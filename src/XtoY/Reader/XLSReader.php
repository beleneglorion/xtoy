<?php

namespace XtoY\Reader;

use XtoY\Reader\ReaderInterface;
use XtoY\Options\Optionnable;
use PHPExcelReader\SpreadsheetReader;
use XtoY\Reporter\ReporterInterface;

class XLSReader extends Optionnable implements ReaderInterface
{
   protected $dsn;
   protected $handler;
   protected $nbRow;
   protected $nbCol;
   protected $currentRow;
   /**
    *
    * @var ReporterInterface 
    */
   protected $reporter;
   

   public function __construct($options)
   {
       parent::__construct();

        $this->addOption('skip','0');
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
        $this->handler = new SpreadsheetReader($filename,false);
        $this->nbRow =   $this->handler->rowcount();
        $this->nbCol =   $this->handler->colcount();
        $this->currentRow = 1;
        if($this->reporter)  { 
           $this->reporter->setTotalLines($this->nbRow);
        } 
       }

   }

   public function close()
   {
      if (isset($this->handler)) {
          unset($this->handler);
      }
   }

   public function fetch()
   {
       $returnValue = false;
       if ($this->currentRow < $this->nbRow) {
        $returnValue = array();
        for ($c = 1;$c <= $this->nbCol;$c++) {
           $returnValue[$c-1] = utf8_encode($this->handler->val($this->currentRow,$c));

        }
        if($this->reporter)  { 
            $this->reporter->setFetchedLines($this->currentRow);
        } 
        $this->currentRow++;

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
       $nbtoSkip = $this->getOption('skip');
       for ($i = 0; $i< $nbtoSkip;$i++) {
           $this->fetch();
       }

   }
   
    public function setReporter(ReporterInterface $reporter) {
       
       $this->reporter = $reporter;
       
       return $this;
   }

}
