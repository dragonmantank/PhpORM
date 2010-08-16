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
 * @package PhpORM_Dao
 */
class PhpORM_Dao_ZendDb extends PhpORM_Dao
{
    /**
     *
     * @var Zend_Db_Table
     */
    protected $_table;
    protected $_tableName;

    public function delete(PhpORM_Entity $entity)
    {
        $table = $this->_getTable();
        $this->delete($entity);
    }

    public function fetchAll($where = null)
    {
        $table = $this->_getTable();
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
        $table = $this->_getTable();
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
        $table = $this->_getTable();
        $select = $table->select();
        if (is_array($key)) {
            foreach ($key as $name => $keyvalue) {
                $select->where($name . ' = ?', $keyvalue);
            }
        } else {
            $select = $select->where($key . ' = ?', $value);
        }
        $result = $table->fetchRow($select);

        return $result->toArray();
    }

    public function find($id)
    {
        $table = $this->_getTable();
        $row = $table->find($id);

        if ($row != null) {
            return $row->toArray();
        } else {
            return null;
        }
    }

    public function insert(PhpORM_Entity $entity)
    {
        $table = $this->_getTable();
        $data = $entity->toArray();

        return $table->insert($data);
    }

    protected function _getTable()
    {
        if ($this->_table == null) {
            $this->_table = new Zend_Db_Table($this->_tableName);
        }

        return $this->_table;
    }

    public function update(PhpORM_Entity $entity)
    {
        $table = $this->_getTable();
        $data = $entity->toArray();
        $id = $entity->id;
        unset($data['id']);

        $table->update($data, "id = '$id'");
    }
}