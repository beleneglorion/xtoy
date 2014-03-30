<?php

namespace XtoY\Mapper;
use XtoY\Reporter\ReporterInterface;

/**
 * Description of MapperInterface
 *
 * @author sebastien
 */
interface MapperInterface
{
    public function convert($line);

    public function batchConvert($table);

    public function setReporter(ReporterInterface $reporter);

}
