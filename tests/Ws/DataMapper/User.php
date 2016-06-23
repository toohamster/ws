<?php

use Ws\DataMapper\DomainObjectAbstract;

class User extends DomainObjectAbstract
{
    public $firstname;
    public $lastname;
    public $username;

    /**
     * Get the full name of the User
     * 
     * Demonstrates how other functions can be
     * added to the DomainObject
     *
     * @return string
     */
    public function getName()
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}