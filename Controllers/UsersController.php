<?php

namespace Controllers;

use \Core\Controller;

class UserController extends Controller{

    public function index()
    {

        $this->returnJson($this->getRequestData());
    }
    public function login()
    {
        $return = array('error' => '');
        $method = $this->getMethod();
        $data = $this->getRequestData();

        if ($method == 'POST') {

            if (!empty($data['email']) && !empty($data['pass'])) {
                
                $users = new Users();

                if ($users->getCredentials($data['email'], $data['senha'])) {

                    $return['jwt'] = $users->createJwt();

                } else {
                    $return['error'] = 'Acesso Negado';
                }

            } else {
                $return['error'] = 'Email e/ou senha não enviados'
            }

        } else {
            $return['error'] = "Método de requisição incompatível";
        }

        $this->returnJson($return);
    }
}