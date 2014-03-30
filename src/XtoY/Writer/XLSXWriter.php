<?php

namespace XtoY\Writer;

use XtoY\Writer\WriterInterface;
use XtoY\Options\Optionnable;
/**
 * A simple of XLSXWriter
 *
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 */
class XLSXWriter  extends Optionnable implements WriterInterface
{
    protected $ddn;
    protected $data;

   public function __construct($options)
   {
       parent::__construct();
        $this->data = array();
        $this->addOption('worksheet','default');
        $this->getOptionManager()->init($options);

   }
    public function setDDN($ddn)
    {
       $this->ddn = $ddn;
    }

    public function getDDN()
    {
      return $this->ddn;
    }

    public function open()
   {
       if (!isset($this->document)) {
        $filename = $this->getDDN();
        if (file_exists($filename)) {
            throw new \Exception(sprintf('File exist (%s)',$filename));
        }
         if (!is_writable(dirname($filename))) {
            throw new \Exception(sprintf('Directeory is not writable (%s)',dirname($filename)));
        }
        $this->document =  new \XLSXWriter();

       }

   }

   public function close()
   {
      if (isset($this->document)) {
          $this->document->writeToFile($this->getDDN());
         
      }
   }

    public function write($line)
    {
        $this->data[] = $line;

    }

    public function writeAll($table)
    {
        $this->data = $table;
    }

    public function postprocessing()
    {
        $options = $this->getOptions();
        $this->document->writeSheet($this->data,$options['worksheet']);
        unset($this->data);
       
    }

   public function preprocessing()
   {
   }


}
