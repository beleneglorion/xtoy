<?php

namespace XtoY\Writer;

/**
 * A simple of CSVWriter
 *
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 */
class CSVWriter  extends FileWriter
{
   protected $document;

   public function __construct($options)
   {
        parent::__construct();

        $this->addOption('delimiter',',');
        $this->addOption('enclosure','"');
        $this->addOption('escaping',true);
        $this->getOptionManager()->init($options);

   }

    public function open()
   {
       if (!isset($this->document)) {
        parent::open();
        $filename = $this->getDDN();
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
        if ($options['escaping']) {
          $values = array_map('XtoY\Writer\SQLWriter::escaping',array_values($line));
        } else {
            $values =array_values($line);
        }

        fputcsv($this->document,$values,$options['delimiter'],$options['enclosure']);
        if ($this->reporter) {
            $this->reporter->setWrittenLines(++$this->line);
        }

    }

    public function writeAll($table)
    {
        foreach ($table  as $line) {
            $this->write($line);
        }

    }

   public static function escaping($str)
   {
         return str_replace(array('"'), array('""'), $str);
   }

}
