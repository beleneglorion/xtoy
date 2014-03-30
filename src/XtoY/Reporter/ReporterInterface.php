<?php

namespace XtoY\Reporter;
/**
 * Description of ReporterInterface
 *
 * @author sebastien
 */
interface ReporterInterface
{
    public function setFetchedLines($fetched);

    public function setTotalLines($total);

    public function setMappedLines($mapped);

    public function setWrittenLines($written);
}
