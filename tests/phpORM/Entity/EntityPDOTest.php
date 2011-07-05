<?php

class Entity_EntityPDOTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Base PDO connection to use
	 * 
	 * @var PDO
	 */
	protected $pdo;
	
	/**
	 * Creates the tables that are needed for the tests and sets the PDO object
	 */
	protected function setUp()
	{
		$this->pdo = new PDO("sqlite:./database.db");
		$this->pdo->exec('CREATE TABLE `sample` (`id` INT, `value` VARCHAR(255));');
		$this->pdo->exec('INSERT INTO sample (`id`, `value`) VALUES (1, "Me!");');
		$this->pdo->exec('INSERT INTO sample (`id`, `value`) VALUES (2, "Too!");');
		$this->pdo->exec('INSERT INTO sample (`id`, `value`) VALUES (3, "Three!");');
		
		PhpORM_Dao_PDO::setConnection($this->pdo);
	}
	
	/**
	 * Destroys the test DB
	 */
	protected function tearDown()
	{
		unlink('./database.db');
	}
	
    public function testSave()
    {
        $entity = new PhpORM_Entity_Generic();
		$dao = new PhpORM_Dao_PDO();
		$dao->setTableName('sample');
        $entity->setDao($dao);
		
		$entity->id = null;
		$entity->value = 'My Value';
		$entity->save();
		
		$this->assertEquals(4, count($dao->fetchAll()));
    }
	
	public function testUpdate()
    {
        $entity = new PhpORM_Entity_Generic();
		$dao = new PhpORM_Dao_PDO();
		$dao->setTableName('sample');
        $entity->setDao($dao);
		
		$entity->fromArray($dao->find(1));
		$entity->value = 'My Value';
		$entity->save();
		
		$data = $dao->find(1);
		$this->assertEquals($entity->toArray(), $data);
		$this->assertEquals($entity->value, $data['value']);
    }
	
	public function testDelete()
	{
		$entity = new PhpORM_Entity_Generic();
		$dao = new PhpORM_Dao_PDO();
		$dao->setTableName('sample');
		$entity->setDao($dao);
		
		$entity->fromArray($dao->find(1));
		$entity->delete();
		
		$this->assertEquals(2, count($dao->fetchAll()));
		$this->assertNull($dao->find(1));
	}
}
