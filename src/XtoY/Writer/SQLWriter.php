<?php

namespace XtoY\Writer;

use XtoY\Writer\WriterInterface;
use XtoY\Options\Optionnable;
/**
 * A simple of SQL_Writer
 *
 * @author Seéastien Thibault <contact@sebastien-thibault.com>
 */
class SQLWriter  extends Optionnable implements WriterInterface
{
    protected $ddn;
    protected $document;

   public function __construct($options)
   {
       parent::__construct();

        $this->addRequiredOption('table');
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
        $options = $this->getOptions();
        $keys = '"'.implode('","',array_map('trim',array_keys($line))).'"';
        // escaping values
        $values = array_map('XtoY\Writer\SQL_Writer::escaping',array_values($line));
        //imploding
        $values = '"'.implode('","',$values).'"';

        $sql = sprintf('INSERT INTO %s (%s) VALUES(%s)'."\n",$options['table'],$keys,$values);
        fputs($this->document,$sql);

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
         return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $str);
   }

}
