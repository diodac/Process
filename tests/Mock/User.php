<?php

namespace Diodac\Process\Test\Mock;


class User
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_APPLICANT = 'ROLE_APPLICANT';
    const ROLE_OFFICIAL = 'ROLE_OFFICIAL';

    private $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }
}