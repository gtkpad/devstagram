<?php

namespace Models;
use \Models\Photos;
use \Models\Users_following;
use \Service\Jwt;

class Users extends \Core\Model
{
    private $id_user;

    public function __construct()
    {
        parent::__construct(['name', 'email', 'pass']);
    }

    public function checkCredentials($email, $pass)
    {
        $data = $this->find(['email' => $email])->fetch();
        if (!is_null($data)) {
            if (password_verify($pass, $data->pass)){
                $this->id_user = $data->id;
                return true;
            } 
        } 
        return false;
    }

    public function register($name, $email, $pass)
    {
        $validate = (new Users())->find(['email' => $email])->fetch();
        if (is_null($validate)) {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $this->data->name = $name;
            $this->data->pass = $hash;
            $this->data->email = $email;
            if ($this->save()) {
                $this->id_user = $this->data->id;
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function getInfo()
    {
        $info = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'following' => count((new Users_following())->find(['id_user_active' => $this->id])->fetch(true)),
            'followers' => count((new Users_following())->find(['id_user_passive' => $this->id])->fetch(true)),
            'photos_count' => count((new Users())->find(['id_user' => $this->id])->fetch(true))
        ];
        if (!is_null($this->avatar)) {
            $info['avatar'] = BASE_URL.'media/avatar/'.$this->avatar;
        } else {
            $info['avatar'] = BASE_URL.'media/avatar/default.jpg';
        }
        return $info;
    }
    public function createJwt()
    {
        return Jwt::create(array('id_user' => $this->id_user));
    }

    public function validateJwt($token)
    {
        $info = Jwt::validate($token);
        if (isset($info->id_user)) {
            return $this->findById($info->id_user);
        } else {
            return null;
        }
    }

}