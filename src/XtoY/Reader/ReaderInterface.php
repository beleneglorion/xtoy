<?php

namespace XtoY\Reader;
use XtoY\Reporter\ReporterInterface;
/**
 *
 * @author beleneglorion
 */
interface ReaderInterface
{
   public function setDSN($dsn);

   public function getDSN();

   public function fetch();

   public function fetchAll();

   public function open();

   public function close();

   public function setReporter(ReporterInterface $reporter);

   public function preprocessing();
}
