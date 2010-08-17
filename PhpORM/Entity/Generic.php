<?php

/**
 * Generic Entity Class
 *
 * Allows generic entities to be created with any attributes. Useful for
 * prototyping.
 *
 * @author Chris Tankersley <chris@ctankersley.com>
 * @copyright 2010 Chris Tankersley
 * @package PhpORM_Entity
 */
class PhpORM_Entity_Generic extends PhpORM_Entity
{
    protected $_ignoreMissingProperty = true;
}
