<?php


namespace XtoY\Reporter;
/**
 * Description of ReporterInterface
 *
 * @author sebastien
 */
interface ReporterInterface
{
    public function setFetchedLines();
    
    public function setTotalLines();
    
    public function setMappedLines();
    
    public function setWrittenLines();
}
