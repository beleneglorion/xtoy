<?php

namespace XtoY\Writer;
use XtoY\Reporter\ReporterInterface;
/**
 * Description of WriterInterface
 *
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 */
interface WriterInterface
{
   public function setDDN($ddn);

   public function write($line);

   public function writeAll($table);

   public function postprocessing();

   public function preprocessing();

   public function open();

   public function close();

   public function setReporter(ReporterInterface $reporter);

   public function rollback();
}
