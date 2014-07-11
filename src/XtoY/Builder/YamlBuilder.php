<?php

namespace XtoY\Builder;

use Symfony\Component\Yaml\Yaml;
/**
 * Description of Builder
 *
 * @author sebastien
 */
class YamlBuilder extends Builder
{
   public static function getConverter($configFile)
   {
       if(!file_exists($configFile)) {
           throw new \Exception('File not found :'.$configFile);
       }
       $config = Yaml::parse($configFile);

       return self::build($config);
   }

}
