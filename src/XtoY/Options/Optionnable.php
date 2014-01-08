<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace XtoY\Options;

/**
 * Description of Optionnable
 *
 * @author sebastien
 */
abstract class Optionnable
{
    protected $optionsManager;

    public function __construct()
    {
        $this->optionsManager = new OptionsManager();

    }
    /**
     * return the option manager of the object
     *
     * @return OptionsManager
     */
    public function getOptionManager()
    {
        return $this->optionsManager;
    }

     public function setOptions($options)
     {

        $this->getOptionManager()->setOptions($options);

        return $this;
     }

     public function getOptions()
     {
      return  $this->getOptionManager()->getOptions();

     }

     public function addOption($optionName, $defaultValue)
     {

       $this->getOptionManager()->addOption($optionName, $defaultValue);

       return $this;
     }

     public function addRequiredOption($optionName)
     {

      $this->getOptionManager()->addRequiredOption($optionName);

      return $this;
     }

     public function setOption($optionName, $value)
     {
        $this->getOptionManager()->setOption($optionName, $value);

        return $this;
     }

     public function getOption($optionName)
     {
        return  $this->getOptionManager()->getOption($optionName);
     }

}
