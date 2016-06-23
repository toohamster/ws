<?php namespace Ws\DataMapper;

use Exception;

/**
 * A basic class describing an object that is part of the Domain Model. 
 * This model assumes that all DomainObjects have an integer as the primary key, 
 * but it would be a trivial change to make it a GUID for example. 
 * It also ensures the ID is immutable.
 */
abstract class DomainObjectAbstract
{
    protected $_id = null;

    /**
     * Get the ID of this object (unique to the
     * object type)
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set the id for this object.
     *
     * @param int $id
     * @return int
     * @throws Exception If the id on the object is already set
     */
    public function setId($id)
    {
        if (!is_null($this->_id)) {
            throw new Exception('ID is immutable');
        }
        return $this->_id = $id;
    }
}