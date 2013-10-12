<?php

require '../vendor/autoload.php';
use XtoY\Reader\CSV_Reader;
use XtoY\Writer\Xliff_Writer;
use XtoY\Mapper\Mapper;
use XtoY\XtoY;
$x = 'input.csv';
$y = 'output.xlf';

@unlink($y);
$csvConfig = array('delimiter'=>';','enclosure'=>'"','escape'=>'\\','length'=>null,'skip'=>1);
$reader = new CSV_Reader($csvConfig);
$reader->setDSN($x);

$xliffConfig = array(
    'source-language' =>'default',
    'target-language' =>'en',
    'original' =>$x
);

$writer = new Xliff_Writer($xliffConfig);
$writer->setDDN($y);

$rules = array();
$rules['source'] = array('src'=>0);
$rules['target'] = array('src'=>2);
$mapper = new Mapper();
$mapper->setRules($rules);



$uc = new XtoY();

$uc->setMapper($mapper);
$uc->setReader($reader);
$uc->setWriter($writer);
$uc->run();

