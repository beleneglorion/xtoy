<?php

require __DIR__.'/../../vendor/autoload.php';



use XtoY\Builder\YamlBuilder;

$uc = YamlBuilder::getConverter('csv2xliff.yml');
$ddn = $uc->getWriter()->getDDN();
@unlinkg($ddn);
$uc->run();

