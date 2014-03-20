<?php

namespace XtoY\Writer;

use XtoY\Writer\WriterInterface;
use XtoY\Options\Optionnable;
use XtoY\Reporter\ReporterInterface;
/**
 * A simple of XliffWriter inspired from symfony XliffFileDumper
 *
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 * @author Michel Salib <michelsalib@hotmail.com>
 */
class XliffWriter  extends Optionnable implements WriterInterface
{
    protected $ddn;
    protected $document;
    protected $body;
    protected $backupFile;
    protected $originalFile;
   protected $line;
   /**
    *
    * @var ReporterInterface 
    */
   protected $reporter;

   public function __construct($options)
   {
       parent::__construct();

        $this->addRequiredOption('source-language');
        $this->addRequiredOption('target-language');
        $this->addOption('overwrite',false);
        $this->addOption('backup',true);
        $this->addOption('original','');
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
        $options = $this->getOptions();   
        $filename = $this->getDDN();
        if (file_exists($filename) && !$options['overwrite']) {
            throw new \Exception(sprintf('File exist (%s)',$filename));
        } 
        if (!is_writable(dirname($filename))) {
            throw new \Exception(sprintf('Directeory is not writable (%s)',dirname($filename)));
        }
        if (file_exists($filename) && $options['overwrite']) {
            if($options['backup']) {
              $this->backup($filename);
            } else {
                @unlink($filename);
            }
        } 
        $this->document = new \DOMDocument('1.0', 'utf-8');
        $this->line = 0;
       }

   }

   public function close()
   {
      if (isset($this->document)) {
         $this->document->save($this->getDDN());
      }
   }

    public function write($line)
    {
        $translation = $this->document->createElement('trans-unit');
        $translation->setAttribute('id', md5($line['source']));
        $translation->setAttribute('resname', $line['source']);
        $s = $translation->appendChild($this->document->createElement('source'));
        $s->appendChild($this->document->createTextNode($line['source']));
        $t = $translation->appendChild($this->document->createElement('target'));
        $t->appendChild($this->document->createTextNode($line['target']));
        $this->body->appendChild($translation);
        if($this->reporter) {
            $this->reporter->setWrittenLines(++$this->line);
        }

    }

    public function writeAll($table)
    {
        foreach ($table as $line) {
            $this->write($line);
        }
    }

      public function postprocessing()
      {
      }

   public function preprocessing()
   {
        $this->document->formatOutput = true;
        $options = $this->getOptions();
        $xliff = $this->document->appendChild($this->document->createElement('xliff'));
        $xliff->setAttribute('version', '1.2');
        $xliff->setAttribute('xmlns', 'urn:oasis:names:tc:xliff:document:1.2');

        $xliffFile = $xliff->appendChild( $this->document->createElement('file'));
        $xliffFile->setAttribute('source-language',$options['source-language']);
        $xliffFile->setAttribute('target-language',$options['target-language']);
        if (!empty($options['original'])) {
          $xliffFile->setAttribute('original',$options['original']);
        }
        $xliffFile->setAttribute('datatype', 'plaintext');

        $this->body = $xliffFile->appendChild($this->document->createElement('body'));
   }
   
   public function  backup($filename) {
       $options = $this->getOptions();    
       $filename = realpath($filename);
       if(!is_boolean($options['backup']) && is_string($options['backup'])) {
          $backupname = dirname($filename) .DIRECTORY_SEPARATOR.$options['backup'];
       } else {
         $backupname = $filename.'.bak';
       }
       if(file_exists($backupname)) {
           unlink($backupname);
       }
        if(rename($filename,$backupname)) {
            $this->backupFile = $backupname;
            $this->originalFile = $filename;
        }
            ;
      
   }
   
   public function rollback()
   {
      if(isset($this->backupFile) && isset( $this->originalFile)) {
          if(file_exists($this->originalFile)) {
            @unlink($this->originalFile);
          }
          rename($this->backupFile,$this->originalFile);
          
      }
        
   }
   
   public function setReporter(ReporterInterface $reporter) {
       
       $this->reporter = $reporter;
       
       return $this;
   }

}
