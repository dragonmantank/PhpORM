<?php
/**
 * This file is part of PhpORM
 *
 * @package PhpORM
 * @license http://opensource.org/licenses/BSD-3-Clause BSD
 */

namespace PhpORM\Repository;

/**
 * Abstract class to build repositories from
 * Sets up some sane defaults for how most repositories will access the storage and create objects.
 *
 * @package PhpORM
 */
abstract class RepositoryAbstract implements RepositoryInterface
{
    /**
     * Copy of class type we should try to create
     * @var object
     */
    protected $prototype;

    /**
     * Storage system we're working against
     * @var object
     */
    protected $storage;

    /**
     * Constructor
     *
     * @param object $storage Storage mechanism to use
     * @param object $prototype Object prototype to build SQL results from
     */
    public function __construct($storage, $prototype)
    {
        $this->storage = $storage;
        $this->prototype = $prototype;
    }

    /**
     * Creates an object from the raw data
     *
     * @return object
     */
    protected function createObject($data)
    {
        $class = get_class($this->prototype);
        $entity = new $class;

        foreach($data as $member => $value) {
            $entity->$member = $value;
        }

        return $entity;
    }
}
