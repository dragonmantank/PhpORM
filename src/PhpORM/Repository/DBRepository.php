<?php
/**
 * This file is part of PhpORM
 *
 * @package PhpORM
 * @license http://opensource.org/licenses/BSD-3-Clause BSD
 */

namespace PhpORM\Repository;

/**
 * Basic Database-backed repository
 *
 * @package PhpORM
 */
class DBRepository extends RepositoryAbstract
{
    /**
     * Column that we should use as the identifier
     * @var string
     */
    protected $identifierColumn = 'id';

    /**
     * Table that we're going to work against
     * @var string
     */
    protected $table;

    /**
     * Constructor
     *
     * @param object $storage Storage mechanism to use
     * @param object $prototype Object prototype to build SQL results from
     */
    public function __construct($storage, $prototype)
    {
        parent::__construct($storage, $prototype);
        $className = explode('\\', get_class($prototype));
        $this->table = strtolower(array_pop($className));
    }

    /**
     * Sets the identifier column, if it needs to be different than the default
     *
     * @param string $identifierColumn
     */
    public function setIdentifierColumn($identifierColumn)
    {
        $this->identifierColumn = $identifierColumn;
    }

    /**
     * Set the table, if it needs to be something other than the auto-generated name
     *
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }
}