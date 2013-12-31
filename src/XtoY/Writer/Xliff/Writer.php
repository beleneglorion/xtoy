<?php

namespace XtoY\Writer;

use XtoY\Writer\WriterInterface;
use XtoY\Options\Optionnable;
/**
 * A simple of Xliff_Writer inspired from symfony XliffFileDumper 
 *
 * @author Seéastien Thibault <contact@sebastien-thibault.com>
 * @author Michel Salib <michelsalib@hotmail.com>
 */
class Xliff_Writer  extends Optionnable implements WriterInterface{

    protected $ddn;
    protected $document;
    protected $body;
     
    
   public function __construct($options) {
       parent::__construct();

        $this->addRequiredOption('source-language');
        $this->addRequiredOption('target-language');
        $this->addOption('original','');
        $this->getOptionManager()->init($options);
        
   } 
    public function setDDN($ddn) {
       $this->ddn = $ddn;
    }
    
    public function getDDN()
    {
      return $this->ddn;
    }
  

    public function open()
   {
       if(!isset($this->document)) {
        $filename = $this->getDDN();
        if(file_exists($filename)) {
            throw new \Exception(sprintf('File exist (%s)',$filename));
        }
         if(!is_writable(dirname($filename))) {
            throw new \Exception(sprintf('Directeory is not writable (%s)',dirname($filename)));
        }
        $this->document = new \DOMDocument('1.0', 'utf-8');


       }
       
       
   }
   
   public function close()
   {
      if(isset($this->document)) { 
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
      
  
    }
    
    public function writeAll($table) {
        foreach($table as $line)
        {
            $this->write($line);
        }
    }
    
      public function  postprocessing(){
          
      }
      
   public function  preprocessing() {
     
        $this->document->formatOutput = true;
        $options = $this->getOptions();
        $xliff = $this->document->appendChild($this->document->createElement('xliff'));
        $xliff->setAttribute('version', '1.2');
        $xliff->setAttribute('xmlns', 'urn:oasis:names:tc:xliff:document:1.2');

        $xliffFile = $xliff->appendChild( $this->document->createElement('file'));
        $xliffFile->setAttribute('source-language',$options['source-language']);
        $xliffFile->setAttribute('target-language',$options['target-language']);
        if(!empty($options['original'])) {
          $xliffFile->setAttribute('original',$options['original']);
        }
        $xliffFile->setAttribute('datatype', 'plaintext');
       // $xliffFile->setAttribute('original', 'file.ext');

        $this->body = $xliffFile->appendChild($this->document->createElement('body'));
   }  
    
}

