<?php namespace Ws\DataMapper;

use DomainObjectAbstract;

/**
 * MapperAbstract is the base class for mappers. 
 * 
 * It defines a standard interface for all mappers.
 *
 * http://blog.tekerson.com/2008/12/17/data-mapper-pattern-in-php/
 */
abstract class MapperAbstract
{

    /**
     * Create a new instance of the DomainObject that this
     * mapper is responsible for. Optionally populating it
     * from a data array.
     *
     * @param array $data
     * @return \Ws\DataMapper\DomainObjectAbstract
     */
    public function create(array $data = null) {
        $obj = $this->_create();
        if ($data) {
            $obj = $this->populate($obj, $data);
        }
        return $obj;
    }

    /**
     * Save the DomainObject
     * 
     * Store the DomainObject in persistent storage. Either insert
     * or update the store as required.
     *
     * @param \Ws\DataMapper\DomainObjectAbstract $obj
     */
    public function save(DomainObjectAbstract $obj)
    {
        if (is_null($obj->getId())) {
            $this->_insert($obj);
        } else {
            $this->_update($obj);
        }
    }

    /**
     * Delete the DomainObject
     * 
     * Delete the DomainObject from persistent storage.
     *
     * @param \Ws\DataMapper\DomainObjectAbstract $obj
     */
    public function delete(DomainObjectAbstract $obj)
    {
        $this->_delete($obj);
    }

    /**
     * Populate the DomainObject with the values
     * from the data array.
     * 
     * To be implemented by the concrete mapper class
     *
     * @param \Ws\DataMapper\DomainObjectAbstract $obj
     * @param array $data
     * @return \Ws\DataMapper\DomainObjectAbstract
     */
    abstract public function populate(DomainObjectAbstract $obj, array $data);

    /**
     * Create a new instance of a DomainObject
     * 
     * @return \Ws\DataMapper\DomainObjectAbstract
     */
    abstract protected function _create();

    /**
     * Insert the DomainObject to persistent storage 
     *
     * @param \Ws\DataMapper\DomainObjectAbstract $obj
     */
    abstract protected function _insert(DomainObjectAbstract $obj);

    /**
     * Update the DomainObject in persistent storage 
     *
     * @param \Ws\DataMapper\DomainObjectAbstract $obj
     */
    abstract protected function _update(DomainObjectAbstract $obj);

    /**
     * Delete the DomainObject from peristent Storage
     *
     * @param \Ws\DataMapper\DomainObjectAbstract $obj
     */
    abstract protected function _delete(DomainObjectAbstract $obj);
    
}