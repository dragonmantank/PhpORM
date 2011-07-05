<?php

/**
 * PDO Backend for data access
 * 
 * Uses PDO for general database access. Requires a valid connection string to
 * be passed, and only currently supports one connection at a time.
 * 
 * @author Chris Tankersley <chris@ctankersley.com>
 * @copyright 2011 Chris Tankersley
 * @package PhpORM_Dao
 */
class PhpORM_Dao_PDO extends PhpORM_Dao
{
	/**
     * Flag to see if the searches should return a True/False result
     */
    const RETURN_BOOL = 1;

    /**
     * Flag to see if the searches should return just one object
     */
    const RETURN_SINGLE = 2;

    /**
     * Flag to see if the searches should return multiple objects
     */
    const RETURN_MULTIPLE = 3;
	
	/**
	 * PDO Connection to use
	 * 
	 * @var PDO
	 */
	static protected $_connection = null;
	
	/**
	 * Table name to use
	 * 
	 * @var string
	 */
	protected $_tableName = null;
	
	public function delete(PhpORM_Entity $entity)
	{
		$where = array(); 
		$values = array();
		foreach((array)$entity as $key => $value) {
			$keypart = trim($key[0]);
			if(!empty($keypart)) {
				$where[] = '`'.$key.'` = ?';
				$values[] = $value;
			}
		}
		$where = 'WHERE '.implode(' AND ', $where);
		
		$conn = $this->getConnection();
		$stmt = $conn->prepare('DELETE FROM '.$this->getTableName().' '.$where);
		for($i = 1; $i < count($values) + 1; $i++) {
			$stmt->bindParam($i, $values[$i - 1]);
		}
		$stmt->execute();
		
		return (bool)$stmt->rowCount();
	}
	
	protected function _executeQuery($key, $value, $type)
	{
		if(!is_array($key)) {
			$key = array($key => $value);
		}

		$where = array();
		$values = array();
		foreach($key as $column => $val) {
			$where[] = '`'.$column.'` = ?';
			$values[] = $val;
		}
		$where = 'WHERE '.implode(' AND ', $where);
		
		$sql = 'SELECT * FROM '.$this->getTableName().' '.$where;
		$conn = $this->getConnection();
		$stmt = $conn->prepare($sql);
		for($i = 1; $i < count($values) + 1; $i++) {
			$stmt->bindParam($i, $values[$i - 1]);
		}
		$stmt->execute();

		switch($type) {
			case self::RETURN_SINGLE:
				$data = $stmt->fetch(PDO::FETCH_ASSOC);
				break;
			case self::RETURN_MULTIPLE:
				$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
				break;
		}
		
		
		if(is_array($data)) {
			return $data;
		} else {
			return null;
		}
	}
	
	public function fetchAll($where = null)
	{
		$conn = $this->getConnection();
		$stmt = $conn->prepare('SELECT * FROM '.$this->getTableName());
		$stmt->execute();

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function fetchAllBy($key, $value = null)
	{
		return $this->_executeQuery($key, $value, self::RETURN_MULTIPLE);
	}
	
	public function fetchOneBy($key, $value = null) 
	{
		return $this->_executeQuery($key, $value, self::RETURN_SINGLE);
	}
	
	public function find($id, $primary = 'id') 
	{
		return $this->fetchOneBy($primary, $id);
	}
	
	/**
	 * Returns the connection object to be used
	 * 
	 * @return PDO
	 * @throws Exception
	 */
	public function getConnection()
	{
		if(self::$_connection == null) {
			throw new Exception('Please set a connection before use');
		}
		
		return self::$_connection;
	}
	
	/**
	 * Returns the table name for this DAO
	 * 
	 * @return string
	 */
	public function getTableName()
	{
		if($this->_tableName == null) {
			throw new Exception('Please set a table name before use');
		}
		
		return $this->_tableName;
	}
	
	public function insert(PhpORM_Entity $entity) 
	{
		$columns = array();
		$values = array();
		foreach((array)$entity as $key => $value) {
			$keypart = trim($key[0]);
			if(!empty($keypart)) {
				$columns[] = $key;
				$placeholders[] = '?';
				$values[] = $value;
			}
		}
		
		$columns = '(`'.implode('`,`', $columns).'`)';
		$placeholders = '('.implode(',', $placeholders).')';
		$query = 'INSERT INTO '.$this->getTableName().' '.$columns.' VALUES '.$placeholders;
		
		$conn = $this->getConnection();
		$stmt = $conn->prepare($query);
		for($i = 1; $i < count($values) + 1; $i++) {
			$stmt->bindParam($i, $values[$i - 1]);
		}
		
		return $stmt->execute();
	}
	
	public function update(PhpORM_Entity $entity, $primary = 'id')
	{
		foreach((array)$entity as $key => $value) {
			$keypart = trim($key[0]);
			if(!empty($keypart)) {
				$set[] = '`'.$key.'` = ?';
				$values[] = $value;
			}
		}
		
		$query = 'UPDATE '.$this->getTableName().' SET '.implode(', ', $set).' WHERE `'.addslashes($primary).'` = ?';
		$conn = $this->getConnection();
		$stmt = $conn->prepare($query);
		for($i = 1; $i < count($values) + 1; $i++) {
			$stmt->bindParam($i, $values[$i - 1]);
		}
		$stmt->bindParam(count($values)+1, $entity->$primary);
		
		return $stmt->execute();
	}
	
	/**
	 * Sets the PDO connection to use
	 * 
	 * @param PDO $connection 
	 */
	static public function setConnection(PDO $connection)
	{
		self::$_connection = $connection;
	}
	
	/**
	 * Sets the table name for this DAO
	 * @param string $tableName 
	 */
	public function setTableName($tableName)
	{
		$this->_tableName = $tableName;
	}
}