<?php

namespace XtoY\Reader;

use XtoY\Reader\ReaderInterface;
use XtoY\Options\Optionnable;

/**
 * A simple of PDOReader
 *
 * @author Sébastien Thibault <contact@sebastien-thibault.com>
 */
class PDOReader extends Optionnable implements ReaderInterface
{
    protected $dsn;

    /**
     *  Database connection handler
     *
     * @var PDO
     */
    protected $dbh;

    /**
     *  Query Statement
     *
     * @var PDOStatement
     */
    protected $stmt;

    public function __construct($options)
    {
        parent::__construct();

        $this->addRequiredOption('query');
        $this->addOption('username', null);
        $this->addOption('password', null);
        $this->getOptionManager()->init($options);
    }

    /**
     *
     * @param  string                 $dsn
     * @return \XtoY\Reader\PDOReader
     */
    public function setDSN($dsn)
    {
        $this->dsn = $dsn;

        return $this;
    }

    public function getDSN()
    {
        return $this->dsn;
    }

    public function open()
    {
        if (!isset($this->dbh)) {

            $options = $this->getOptions();
            $dsn = $this->getDSN();
            try {
                $this->dbh = new \PDO($dsn, $options['username'], $options['password']);
            } catch (\PDOException $e) {
                throw new \Exception(sprintf('Can\'t connect to database %s (%s)', $dsn, $e->getMessage()));
            }
            $this->stmt = $this->dbh->query($options['query']);
            if (!$this->stmt) {
                $info= $this->dbh->errorInfo();
                throw new \PDOException($info[2], $info[1]);
            }

       if ($this->reporter) {
           $this->reporter->setTotalLines( $this->stmt->rowCount());
       }

        }
    }

    public function close()
    {
        if (isset($this->dbh)) {
            unset($this->dbh);
        }
    }

    public function fetch()
    {
        return $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchAll()
    {
        return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function preprocessing()
    {
    }

    public function setReporter(ReporterInterface $reporter)
   {
       $this->reporter = $reporter;

       return $this;
   }

}
