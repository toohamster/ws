<?php

use Ws\DataMapper\MapperAbstract;
use Ws\DataMapper\DomainObjectAbstract;

class UserMapper extends MapperAbstract
{
    /**
     * Fetch a user object by ID
     * 
     * An example skeleton of a "Fetch" function showing
     * how the database data ($dataFromDb) is used to
     * create a new User instance via the create function.
     *
     * @param string $id
     * @return User
     */
    public function findById($id)
    {
        // Query database for User with $id
        // ...
        $dataFromDb = array(
                'id'        => $id,
                'firstname' => 'Brenton',
                'lastname'  => '',
                'username'  => 'Tekerson',
            );
        return $this->create($dataFromDb);
    }

    /**
     * Poplate the User (DomainObject) with
     * the data array.
     * 
     * This is a very simple example, but the mapping 
     * can be as complex as required.
     *
     * @param DomainObjectAbstract $obj
     * @param array $data
     * @return User
     */
    public function populate(DomainObjectAbstract $obj, array $data)
    {
        $obj->setId($data['id']);
        $obj->firstname = $data['firstname'];
        $obj->lastname  = $data['lastname'];
        $obj->username  = $data['username'];
        return $obj;
    }

    /**
     * Create a new User DomainObject
     *
     * @return User
     */
    protected function _create()
    {
        return new User();
    }

    /**
     * Insert the DomainObject in persistent storage
     * 
     * This may include connecting to the database
     * and running an insert statement.
     *
     * @param DomainObjectAbstract $obj
     */
    protected function _insert(DomainObjectAbstract $obj)
    {
        // ...
    }

    /**
     * Update the DomainObject in persistent storage
     * 
     * This may include connecting to the database
     * and running an update statement.
     *
     * @param DomainObjectAbstract $obj
     */
    protected function _update(DomainObjectAbstract $obj)
    {
        // ...
    }

    /**
     * Delete the DomainObject from persistent storage
     * 
     * This may include connecting to the database
     * and running a delete statement.
     *
     * @param DomainObjectAbstract $obj
     */
    protected function _delete(DomainObjectAbstract $obj)
    {
        // ...
    }
}