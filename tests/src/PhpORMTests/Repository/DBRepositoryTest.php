<?php

/**
 * This file is part of PhpORM
 *
 * @package PhpORM
 * @license http://opensource.org/licenses/BSD-3-Clause BSD
 */

namespace PhpORMTests\Repository;

use PhpORM\Repository\DBRepository;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class DBRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Makes sure that the underlying Delete method is called properly against the storage
     *
     * @since 2014-04-15
     */
    public function testDelete()
    {
        $storageMock = $this->getMock('\\stdClass', array('delete'));
        $storageMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->equalTo(array('id' => 1)), $this->stringContains('users'))
            ->willReturn(1)
        ;

        $repo = new DBRepository($storageMock, new \stdClass);
        $repo->setTable('users');
        $response = $repo->delete(array('id' => 1));

        $this->assertEquals(1, $response);
    }

    /**
     * Makes sure that the underlying Fetch All method is called properly against the storage
     *
     * @since 2014-04-15
     */
    public function testFetchAll()
    {
        $row1 = new \stdClass();
        $row1->id = 1;
        $row1->username = 'root';
        $expectedData = array($row1);
        $storageMock = $this->getMock('\\stdClass', array('fetchAll'));
        $storageMock
            ->expects($this->once())
            ->method('fetchAll')
            ->with($this->stringContains('users'))
            ->willReturn(array(array('id' => 1, 'username' => 'root')));
        ;

        $repo = new DBRepository($storageMock, new \stdClass);
        $repo->setTable('users');
        $response = $repo->fetchAll();

        $this->assertEquals($expectedData, $response);
    }

    /**
     * Makes sure that the underlying Fetch All By method is called properly against the storage
     *
     * @since 2014-04-15
     */
    public function testFetchAllBy()
    {
        $row1 = new \stdClass();
        $row1->id = 1;
        $row1->username = 'root';
        $expectedData = array($row1);
        $storageMock = $this->getMock('\\stdClass', array('fetchAllBy'));
        $storageMock
            ->expects($this->once())
            ->method('fetchAllBy')
            ->with($this->equalTo(array('id' => 1)), $this->stringContains('users'))
            ->willReturn(array(array('id' => 1, 'username' => 'root')));
        ;

        $repo = new DBRepository($storageMock, new \stdClass);
        $repo->setTable('users');
        $response = $repo->fetchAllBy(array('id' => 1));

        $this->assertEquals($expectedData, $response);
    }

    /**
     * Makes sure that the underlying Find method is called properly against the storage using the correct columns
     *
     * @since 2014-04-15
     */
    public function testFind()
    {
        $row1 = new \stdClass();
        $row1->id = 1;
        $row1->username = 'root';
        $storageMock = $this->getMock('\\stdClass', array('find'));
        $storageMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(array('id' => 1)), $this->stringContains('users'))
            ->willReturn(array('id' => 1, 'username' => 'root'));
        ;

        $repo = new DBRepository($storageMock, new \stdClass);
        $repo->setTable('users');
        $response = $repo->find(1);

        $this->assertEquals($row1, $response);
    }

    /**
     * Makes sure that the Find result returns null if nothing is found
     *
     * @since 2014-04-15
     */
    public function testFindReturnsNull()
    {
        $storageMock = $this->getMock('\\stdClass', array('find'));
        $storageMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(array('id' => 1)), $this->stringContains('users'))
            ->willReturn(array());
        ;

        $repo = new DBRepository($storageMock, new \stdClass);
        $repo->setTable('users');
        $response = $repo->find(1);

        $this->assertNull($response);
    }

    /**
     * Makes sure that the underlying Find By method is called properly against the storage using the correct columns
     *
     * @since 2014-04-15
     */
    public function testFindBy()
    {
        $row1 = new \stdClass();
        $row1->id = 1;
        $row1->username = 'root';
        $storageMock = $this->getMock('\\stdClass', array('find'));
        $storageMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(array('id' => 1)), $this->stringContains('users'))
            ->willReturn(array('id' => 1, 'username' => 'root'));
        ;

        $repo = new DBRepository($storageMock, new \stdClass);
        $repo->setTable('users');
        $response = $repo->findBy(array('id' => 1));

        $this->assertEquals($row1, $response);
    }

    /**
     * Makes sure that the Find By result returns null if nothing is found
     *
     * @since 2014-04-15
     */
    public function testFindByReturnsNull()
    {
        $storageMock = $this->getMock('\\stdClass', array('find'));
        $storageMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(array('id' => 1)), $this->stringContains('users'))
            ->willReturn(array());
        ;

        $repo = new DBRepository($storageMock, new \stdClass);
        $repo->setTable('users');
        $response = $repo->findBy(array('id' => 1));

        $this->assertNull($response);
    }

    /**
     * Makes sure that the underlying Save method is called properly against the storage
     *
     * @since 2014-04-15
     */
    public function testSave()
    {
        $storageMock = $this->getMock('\\stdClass', array('save'));
        $storageMock
            ->expects($this->once())
            ->method('save')
            ->with($this->equalTo(array('username' => 'root')), $this->stringContains('users'))
            ->willReturn(1)
        ;

        $repo = new DBRepository($storageMock, new \stdClass);
        $repo->setTable('users');
        $response = $repo->save(array('username' => 'root'));

        $this->assertEquals(1, $response);
    }
}
