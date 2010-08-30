<?php

/**
 * Generates an SQLite database based upon entities
 *
 * @author Chris Tankersley <chris@ctankersley.com>
 * @copyright 2010 Chris Tankersley
 * @package PhpORM_Cli
 */
class PhpORM_Cli_CreateSQLiteDb
{
    /**
     * Array of entities that we need to parse to create the databse
     * @var <array
     */
    protected $_entities;

    /**
     * Database connection
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * Creates an SQLite Database of the specified name
     *
     * The name should be the full path to wherever the file needs to reside
     * @param string $dbName
     */
    public function create($dbName)
    {
        if($this->_entities == null || $this->_db == null) {
            throw new Exception('Please set the list of entities and a database connection');
        } else {
            if(is_file($dbName)) {
                unlink($dbName);
            }

            foreach($this->_entities as $class) {
                $entity = new $class();
                $sqlGen = new PhpORM_Cli_GenerateSql($class, $entity->getDao()->getTableName(), PhpORM_Cli_GenerateSql::SQLITE);
                $sql = $sqlGen->getSql();

                $this->_db->query($sql);
            }
        }
    }

    /**
     * Sets the database object
     * @param Zend_Db_Adapter_Abstract $db
     */
    public function setDb(Zend_Db_Adapter_Abstract $db)
    {
        $this->_db = $db;
    }

    /**
     * Array of entities to parse
     * @param array $entities
     */
    public function setEntities(array $entities)
    {
        $this->_entities = $entities;
    }
}