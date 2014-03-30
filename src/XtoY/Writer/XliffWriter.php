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
    protected $document;
    protected $body;
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
        $this->getOptionManager()->init($options);

   }

    public function open()
   {
       if (!isset($this->document)) {
        parent::open();
         $this->document = new \DOMDocument('1.0', 'utf-8');
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
        if ($this->reporter) {
            $this->reporter->setWrittenLines(++$this->line);
        }

    }

    public function writeAll($table)
    {
        foreach ($table as $line) {
            $this->write($line);
        }
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

}
