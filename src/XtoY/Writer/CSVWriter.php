<?php

namespace XtoY\Writer;

use XtoY\Writer\WriterInterface;
use XtoY\Options\Optionnable;
/**
 * A simple of CSVWriter
 *
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 */
class CSVWriter  extends Optionnable implements WriterInterface
{
    protected $ddn;
    protected $document;

   public function __construct($options)
   {
       parent::__construct();

        $this->addOption('delimiter',',');
        $this->addOption('enclosure','"');
        $this->addOption('escaping',true);
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
        $this->document = fopen($filename,'w');

       }

   }

   public function close()
   {
      if (isset($this->document)) {
         fclose($this->document);
      }
   }

    public function write($line)
    {
        //int fputcsv ( resource $handle , array $fields [, string $delimiter = ',' [, string $enclosure = '"' ]] )
        $options = $this->getOptions();
        // escaping values
        if($options['escaping']) {
          $values = array_map('XtoY\Writer\SQL_Writer::escaping',array_values($line));
        } else {
            $values =array_values($line);
        }
        
        

        fputcsv($this->document,$values,$options['delimiter'],$options['enclosure']);

    }

    public function writeAll($table)
    {
        foreach ($table  as $line) {
            $this->write($line);
        }

    }

    public function postprocessing()
    {
    }

   public function preprocessing()
   {
   }

   public static function escaping($str)
   {
         return str_replace(array('"'), array('""'), $str);
   }

}
