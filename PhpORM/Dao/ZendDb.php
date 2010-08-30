<?php

/**
 * ZendDB storage mechanism for PhpORM
 *
 * Uses ZendDb to query an external database to get schemas and build
 * Zend_Db_Table objects on the fly. Requires proper setup of Zend_Db before
 * usage. Will work with a Zend Framework application that sets up Zend_Db
 * via configuration files.
 *
 * @author Chris Tankersley <chris@ctankersley.com>
 * @copyright 2010 Chris Tankersley
 * @package PhpORM_Dao
 */
class PhpORM_Dao_ZendDb extends PhpORM_Dao
{
    /**
     * Table object
     * @var Zend_Db_Table
     */
    protected $_table;

    /**
     * Name of the table we should generate from
     * @var <type>
     */
    protected $_tableName;

    /**
     * Deletes the specified entity from the database
     * @param PhpORM_Entity $entity
     * @return bool
     */
    public function delete(PhpORM_Entity $entity)
    {
        $table = $this->getTable();
        return $table->delete($table->getAdapter()->quoteInto('id = ?',$entity->id));
    }

    /**
     * Selects all of the matching rows in a database
     * @param string $where
     * @return array
     */
    public function fetchAll($where = null)
    {
        $table = $this->getTable();
        if ($where != null) {
            $result = $table->fetchAll($where);
        } else {
            $result = $table->fetchAll();
        }

        return $result->toArray();
    }

    /**
     * Performs a search based upon a specific column
     *
     * If $key is an array, value is ignored and $key should be in
     * 'columnname' => 'value'.
     *
     * @param mixed $key
     * @param mixed $value
     * @return array
     */
    public function fetchAllBy($key, $value = null)
    {
        $table = $this->getTable();
        $select = $table->select();
        if (is_array($key)) {
            foreach ($key as $name => $keyvalue) {
                $select->where($name . ' = ?', $keyvalue);
            }
        } else {
            $select = $select->where($key . ' = ?', $value);
        }
        $result = $table->fetchAll($select);

        return $result->toArray();
    }

    /**
     * Performs a search based upon a specific column and returns a single row
     *
     * If $key is an array, value is ignored and $key should be in
     * 'columnname' => 'value'.
     *
     * @param mixed $key
     * @param mixed $value
     * @return array
     */
    public function fetchOneBy($key, $value = null)
    {
        $table = $this->getTable();
        $select = $table->select();
        if (is_array($key)) {
            foreach ($key as $name => $keyvalue) {
                $select->where($name . ' = ?', $keyvalue);
            }
        } else {
            $select = $select->where($key . ' = ?', $value);
        }
        $result = $table->fetchRow($select);

        if($result != null) {
            return $result->toArray();
        } else {
            return null;
        }
    }

    /**
     * Returns the row that has the specified ID
     *
     * If no row is found, null is returned
     * 
     * @param mixed $id
     * @return mixed
     */
    public function find($id)
    {
        $table = $this->getTable();
        $row = $table->find($id);

        if ($row != null) {
            return $row->toArray();
        } else {
            return null;
        }
    }

    /**
     * Returns the table name
     * @return string
     */
    public function getTableName()
    {
        return $this->_tableName;
    }

    /**
     * Inserts the entity into the database
     * @param PhpORM_Entity $entity
     * @return integer
     */
    public function insert(PhpORM_Entity $entity)
    {
        $table = $this->getTable();
        $data = $entity->toArray();

        return $table->insert($data);
    }

    /**
     * Gets the Zend_Db_Table object
     * @return Zend_Db_Table_Abstract
     */
    public function getTable()
    {
        if ($this->_table == null) {
            $this->_table = new Zend_Db_Table($this->_tableName);
        }

        return $this->_table;
    }

    /**
     * Updates the specified row with entity data
     * @param PhpORM_Entity $entity
     */
    public function update(PhpORM_Entity $entity)
    {
        $table = $this->getTable();
        $data = $entity->toArray();
        $id = $entity->id;
        unset($data['id']);

        $table->update($data, "id = '$id'");
    }
}