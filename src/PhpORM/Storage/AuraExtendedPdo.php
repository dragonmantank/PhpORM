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
     * @var \Aura\SqlQuery\QueryFactory
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
     * Builds a select query
     *
     * @param $table
     * @param array $cols
     * @param string $order
     * @return \Aura\SqlQuery\Common\SelectInterface
     */
    protected function buildSelectQuery($table, $criteria = array(), $cols = array('*'), $order = 'id ASC')
    {
        $select = $this->queryHandler->newSelect();
        $select
            ->cols($cols)
            ->from($table)
            ->orderBy(array($order))
        ;

        foreach($criteria as $column => $value) {
            $select->where($column.' = :'.$column);
        }

        if(!empty($criteria)) {
            $select->bindValues($criteria);
        }

        return $select;
    }

    public function delete($criteria, $table)
    {
        $delete = $this->queryHandler->newDelete();
        $delete->from($table);
        foreach($criteria as $col => $value) {
            $delete->where($col.' = :'.$col);
        }
        $delete->bindValues($criteria);
        
        return $this->db->perform($delete->__toString(), $delete->getBindValues());
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
        $select = $this->buildSelectQuery($table, array(), array('*'), $order);

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
        $select = $this->buildSelectQuery($table, $criteria, array('*'), $order);

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
        $select = $this->buildSelectQuery($table, $criteria);

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
        $data = $this->convertToArray($data);
        if(!empty($data[$identifierColumn])) {
            $update = $this->queryHandler->newUpdate();
            $update
                ->table($table)
                ->cols(array_keys($data))
                ->where($identifierColumn.' = :'.$identifierColumn)
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

    /**
     * Tries various ways to convert the entity to an array safely
     *
     * @param mixed $data
     * @return array
     */
    protected function convertToArray($data)
    {
        if(is_array($data)) {
            return $data;
        }

        if(is_object($data)) {
            if(method_exists($data, 'toArray')) {
                return $data->toArray();
            }
        }

        return (array)$data;
    }
}