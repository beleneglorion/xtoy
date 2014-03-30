<?php

namespace XtoY\Writer;

use XtoY\Writer\WriterInterface;
use XtoY\Options\Optionnable;
/**
 * Search Engine Sitemap Writer
 *
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 */
class SitemapWriter  extends Optionnable implements WriterInterface
{
    protected $ddn;
    protected $document;
    protected $urlset;

   public function __construct($options)
   {
       parent::__construct();

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
        $url = $this->document->createElement('url');
        $loc = $this->document->createElement('loc',$line['loc']);
        $url->appendChild($loc);
        if(isset($line['lastmod'])) {
            $child = $this->document->createElement('lastmod',$line['lastmod']);
            $url->appendChild($child);
        }
        if(isset($line['changefreq'])) {
            $child = $this->document->createElement('changefreq',$line['changefreq']);
            $url->appendChild($child);
        }
        if(isset($line['priority'])) {
            $child = $this->document->createElement('priority',$line['priority']);
            $url->appendChild($child);
        }
        $this->urlset->appendChild($url);

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
        $this->urlset = $this->document->appendChild($this->document->createElement('urlset'));
        $this->urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
   }

}
