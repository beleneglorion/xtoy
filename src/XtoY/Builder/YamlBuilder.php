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
       $config = Yaml::parse($configFile);

       return self::build($config);
   }

}
