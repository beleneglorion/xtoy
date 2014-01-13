<?php

namespace XtoY\Writer;

use XtoY\Writer\WriterInterface;
use XtoY\Options\Optionnable;
/**
 * A simple of PDOWriter
 *
 * @author Seéastien Thibault <contact@sebastien-thibault.com>
 */
class PDOUpdater  extends Optionnable implements WriterInterface
{
    protected $ddn;
    /**
     *  Database connection handler
     *
     * @var PDO
     */
    protected $dbh;

   public function __construct($options)
   {
       parent::__construct();

        $this->addRequiredOption('table');
        $this->addRequiredOption('where');
        $this->addOption('username',null);
        $this->addOption('password',null);
        $this->addOption('driver_options',null);
        $this->addOption('transaction',false);
        $this->getOptionManager()->init($options);

   }
    public function setDDN($ddn)
    {
       $this->ddn = $ddn;
    }

    public function getDDN()
    {
      return $this->ddn;
    }

    public function open()
   {
       if (!isset($this->dbh)) {

            $options = $this->getOptions();
            $driverOptions = array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);
            $dsn = $this->getDDN();
            if(isset($options['driver_options'])) {
                $driverOptions = $driverOptions+ $options['driver_options'];
            }
            try {
                $this->dbh = new \PDO($dsn, $options['username'], $options['password'], $driverOptions);
            } catch (\PDOException $e) {
                throw new \Exception(sprintf('Can\'t connect to database %s (%s)',$dsn,$e->getMessage()));
            }
            if ($options['transaction']) {
               $this->dbh->beginTransaction();
            }       
       }

   }

   public function close()
   {

      if (isset($this->dbh)) {
        $options = $this->getOptions();
        if ($options['transaction']) {
           $this->dbh->commit();
        }
        unset($this->dbh);
      }
   }

    public function write($line)
    {

        $options = $this->getOptions();
        $cond = array();
        foreach($options['where'] as $f) {
             $cond[] = $f.'='.$this->dbh->quote($line[$f]);
             unset($line[$f]); // if in where doesn't need to be updated
        }
        
        $fields = array();
        foreach ($line as $k => $v) {
             $fields[] = $k.'='.$this->dbh->quote($v);
        }
        
        $sql = sprintf('UPDATE %s SET %s WHERE %s' ."\n",$options['table'],implode(',',$fields),implode(' AND ',$cond));
        $sth = $this->dbh->exec($sql);
       
    }

    public function writeAll($table)
    {
        foreach ($table  as $line) {
           $this>-write($line);
        }
    }

    public function postprocessing()
    {
    }

   public function preprocessing()
   {
   }

}
