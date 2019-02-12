<?php

namespace One\CheckJeHuis\Entity;

use FOS\UserBundle\Model\User as BaseUser;

class User extends BaseUser
{
    protected $id;

    protected $name;

    protected $city;

    protected $availableRoles = [
        'ROLE_ADMIN' => 'Admin',
        'ROLE_CITY' => 'Gemeente',
    ];

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    public function displayRoles()
    {
        $userRoles = '';
        foreach ($this->roles as $role) {
            $userRoles .= $this->availableRoles[$role] . ',';
        }
        $userRoles = rtrim($userRoles, ',');

        return $userRoles;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->username = $name;
    }

    public function isAdmin()
    {
        if (in_array('ROLE_ADMIN', $this->getRoles(), true)) {
            return true;
        }

        return false;
    }
}
