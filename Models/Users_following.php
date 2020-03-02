<?php

namespace Models;

class Users_following extends \Core\Model
{


    public function __construct()
    {
        parent::__construct(['id_user_active', 'id_user_passive']);
    }
}