<?php

require __DIR__.'/../../vendor/autoload.php';
use XtoY\Builder\YamlBuilder;
chdir(__DIR__);
$uc = YamlBuilder::getConverter('xlsx2xliff.yml');
$ddn = $uc->getWriter()->getDDN();
if(file_exists($ddn)) {
 unlink($ddn);   
};
$uc->run();

