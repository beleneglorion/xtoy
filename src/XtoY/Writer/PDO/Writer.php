<?php

namespace XtoY\Writer;

use XtoY\Writer\WriterInterface;
use XtoY\Options\Optionnable;
/**
 * A simple of PDO_Writer 
 *
 * @author Seéastien Thibault <contact@sebastien-thibault.com>
 */
class PDO_Writer  extends Optionnable implements WriterInterface{

    protected $ddn;
    /**
     *  Database connection handler
     * 
     * @var PDO 
     */
    protected $dbh;
     
    
   public function __construct($options) {
       parent::__construct();

        $this->addRequiredOption('table');
        $this->addOption('username',null);
        $this->addOption('password',null);
        $this->addOption('transaction',false);
        $this->getOptionManager()->init($options);
        
   } 
    public function setDDN($ddn) {
       $this->ddn = $ddn;
    }
    
    public function getDDN()
    {
      return $this->ddn;
    }
  

    public function open()
   {
       if(!isset($this->dbh)) {
           
            $options = $this->getOptions();
            $dsn = $this->getDDN();
            try {
                $this->dbh = new \PDO($dsn, $options['username'], $options['password']);
            } catch (\PDOException $e) {
                throw new \Exception(sprintf('Can\'t connect to database %s (%s)',$dsn,$e->getMessage()));
            }
            if($options['transaction']) {
               $this->dbh->beginTransaction();
            }
       }
       
       
       
   }
   
   public function close()
   {
    
      if(isset($this->dbh)) { 
        $options = $this->getOptions();  
        if($options['transaction']) {
           $this->dbh->commit();
        }  
        unset($this->dbh);
      }
   }
   
    public function write($line)
    {
  
        $options = $this->getOptions();
        $keys = '"'.implode('","',array_map('trim',array_keys($line))).'"';
        $marks = implode(',',array_fill(0, count(array_keys($line)), '?'));
        $sql = sprintf('INSERT INTO %s (%s) VALUES(%s)'."\n",$options['table'],$keys,$marks);
        $sth = $this->dbh->prepare($sql);
        $sth->execute(array_values($line));
      
    }
    
    public function writeAll($table) {
        
        $options = $this->getOptions();
        $firstLine = current($table);
        $keys = '"'.implode('","',array_map('trim',array_keys($firstLine))).'"';
        $marks = implode(',',array_fill(0, count(array_keys($firstLine)), '?'));
        try {
             /* Insérer plusieurs enregistrements sur une base tout-ou-rien */
             $sql = sprintf('INSERT INTO %s (%s) VALUES(%s)'."\n",$options['table'],$keys,$marks);

            $sth = $this->dbh->prepare($sql);
            foreach($table  as $line) {
               $sth->execute(array_values($line));
            }
        } catch (\PDOException $e) {
            if($options['transaction']) {
              $this->dbh->rollback();
            }
            throw new \Exception($e->getMessage());
        }

    }
    
    public function  postprocessing(){

    }

   public function  preprocessing() {
     
        
   }  
   
   
    
}

