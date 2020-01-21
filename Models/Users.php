<?php

namespace Models;
use \Service\Jwt;

class Users extends \Core\Model
{
    private $id_user;

    public function __construct($table = null)
    {
        parent::__construct($table);
    }

    public function checkCredentials($email, $pass)
    {
        $data = $this->find(['email' => $email])->fetch();
        if (!is_null($data)) {
            if (password_verify($senha, $data->pass)){
                $this->id_user = $data->id;
                return true;
            } 
        } 
        return false;
    }

    public function createJwt()
    {
        return Jwt::create(array('id_user' => $this->id_user));

    }

}