<?php

/**
 * This file is part of PhpORM
 *
 * @package PhpORM
 * @license http://opensource.org/licenses/BSD-3-Clause BSD
 */
namespace PhpORM\Storage;

/**
 * Database storage container that uses Aura SQL and Aura SQL-Query to access the database
 *
 * @package PhpORM
 */
class AuraExtendedPdo
{
    /**
     * Connection to the Database
     * @var \Aura\SQL\ExtendedPdo
     */
    protected $db;

    /**
     * Fluent SQL handler
     * @var \Aura\Sql_Query\QueryFactory
     */
    protected $queryHandler;

    /**
     * Constructor
     *
     * @param $db
     * @param $queryHandler
     */
    public function __construct($db, $queryHandler)
    {
        $this->db = $db;
        $this->queryHandler = $queryHandler;
    }

    /**
     * Returns all the results in the table
     *
     * @param string $table Table to return from
     * @param string $order SQL order clause
     * @return array
     */
    public function fetchAll($table, $order = 'id ASC')
    {
        $select = $this->queryHandler->newSelect();
        $select
            ->cols(array('*'))
            ->from($table)
            ->orderBy(array($order))
        ;

        return $this->db->fetchAll($select->__toString());
    }

    /**
     * Returns all the results in the table that match the specified criteria
     * Criteria must be an array, with the DB column the key and the DB value the value
     *
     * @param array $criteria Search criteria
     * @param string $table Table to search against
     * @param string $order SQL order clause
     * @return array
     */
    public function fetchAllBy($criteria, $table, $order = 'id ASC')
    {
        $select = $this->queryHandler->newSelect();
        $select
            ->cols(array('*'))
            ->from($table)
            ->orderBy(array($order))
        ;
        foreach($criteria as $column => $value) {
            $select->where($column.' = :'.$column);
        }
        $select->bindValues($criteria);

        return $this->db->fetchAll($select->__toString(), $select->getBindValues());
    }

    /**
     * Returns a single result based on the criteria
     * Criteria must be an array, with the DB column the key and the DB value the value
     *
     * @param array $criteria Search criteria
     * @param string $table Table to search against
     * @return array
     */
    public function find($criteria, $table)
    {
        $select = $this->queryHandler->newSelect();
        $select
            ->cols(array('*'))
            ->from($table)
        ;
        foreach($criteria as $column => $value) {
            $select->where($column.' = :'.$column);
        }
        $select->bindValues($criteria);

        return $this->db->fetchOne($select->__toString(), $select->getBindValues());
    }

    /**
     * Saves a set of data to the table
     * This function will either insert or update, depending on if the entity passed already has an identifier set. The
     * generated/passed ID will be returned.
     *
     * @param object|array $data Data to save
     * @param string $table Table to save to
     * @param string $identifierColumn Identifier column to work against
     * @return int|string
     */
    public function save($data, $table, $identifierColumn = 'id')
    {
        $data = (array)$data;
        if(!empty($data[$identifierColumn])) {
            $update = $this->queryHandler->newUpdate();
            $update
                ->table($table)
                ->cols(array_keys($data))
                ->bindValues($data)
            ;
            $this->db->perform($update->__toString(), $update->getBindValues());
            return $data[$identifierColumn];
        } else {
            $insert = $this->queryHandler->newInsert();
            $insert
                ->into($table)
                ->cols(array_keys($data))
                ->bindValues($data)
            ;
            $this->db->perform($insert->__toString(), $insert->getBindValues());
            $name = $insert->getLastInsertIdName($identifierColumn);
            return $this->db->lastInsertId($name);
        }
    }
}