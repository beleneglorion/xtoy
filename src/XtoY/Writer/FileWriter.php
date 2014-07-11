<?php

namespace XtoY\Writer;

use XtoY\Writer\WriterInterface;
use XtoY\Options\Optionnable;
use XtoY\Reporter\ReporterInterface;
/**
 * Abstract Class that manage backup/restore file when writting file
 *
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 */
abstract class FileWriter  extends Optionnable implements WriterInterface
{
    protected $ddn;
    protected $backupFile;
    protected $originalFile;
    protected $line;
    protected $reporter;

   public function __construct()
   {
       parent::__construct();

        $this->addOption('overwrite',false);
        $this->addOption('backup',true);

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

        $options = $this->getOptions();
        $filename = $this->getDDN();
        if (file_exists($filename) && !$options['overwrite']) {
            throw new \Exception(sprintf('File exist (%s)',$filename));
        }
        if (!is_writable(dirname($filename))) {
            throw new \Exception(sprintf('Directory is not writable (%s)',dirname($filename)));
        }
        if (file_exists($filename) && $options['overwrite']) {
            if ($options['backup']) {
              $this->backup($filename);
            } else {
                @unlink($filename);
            }
        }
        $this->line = 0;

   }

   public function close()
   {

   }

   public function postprocessing()
    {
    }

   public function preprocessing()
   {
   }

   public function setReporter(ReporterInterface $reporter)
       {
       $this->reporter = $reporter;

       return $this;
   }

    public function rollback()
     {
      if (isset($this->backupFile) && isset( $this->originalFile)) {
          if (file_exists($this->originalFile)) {
            @unlink($this->originalFile);
          }
          rename($this->backupFile,$this->originalFile);

      }
     }

   public function backup($filename)
   {
       $options = $this->getOptions();
       $filename = realpath($filename);
       if (!is_bool($options['backup']) && is_string($options['backup'])) {
          $backupname = dirname($filename) .DIRECTORY_SEPARATOR.$options['backup'];
       } else {
         $backupname = $filename.'.bak';
       }
       if (file_exists($backupname)) {
           unlink($backupname);
       }
        if (rename($filename,$backupname)) {
            $this->backupFile = $backupname;
            $this->originalFile = $filename;
        }

   }
}
