<?php

namespace Controllers;

use \Core\Controller;
use \Models\Users;

class UsersController extends Controller
{

    public function index()
    {
        $this->returnJson($this->getRequestData());
    }

    public function login()
    {
        $response = array('error' => '');
        $method = $this->getMethod();
        $data = $this->getRequestData();

        if ($method == 'POST') {

            if (!empty($data['email']) && !empty($data['pass'])) {

                $users = new Users();
                if ($users->checkCredentials($data['email'], $data['pass'])) {

                    $response['jwt'] = $users->createJwt();

                } else {
                    $response['error'] = 'Acesso Negado';
                }

            } else {
                $response['error'] = 'Email e/ou senha não enviados';
            }

        } else {
            $response['error'] = "Método de requisição incompatível";
        }

        $this->returnJson($response);
    }

    public function new_record()
    {
        $response = ['error' => ''];
        $method = $this->getMethod();
        $data = $this->getRequestData();

        if ($method == 'POST') {
            if (!empty($data['name']) && !empty($data['email']) && !empty($data['pass'])) {
                if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $users = new Users();
                    if ($users->register($data['name'], $data['email'], $data['pass'])) {
                        $response['jwt'] = $users->createJwt();
                    } else {
                        $response['error'] = 'Email já cadastrado';
                    }
                } else {
                    $response['error'] = 'Email inválido';
                }
            } else {
                $response['error'] = 'Dados não preenchidos';
            }
        } else {
            $response['error'] = "Método de requisição incompatível";
        }
        $this->returnJson($response);
    }

    public function view($id)
    {
        $response = ['error' => '', 'logged' => false];
        $method = $this->getMethod();
        $data = $this->getRequestData();

        if (!empty($data['jwt'])) {
            $user = (new Users())->validateJwt($data['jwt']);
            if (!is_null($user)) {
                $response['logged'] = true;
                $response['is_me'] = false;
                if ($id == $user->id) $response['is_me'] = true;
                    $validate = (new Users())->findById($id);
                    if (!is_null($validate)) {
                        switch ($method) {
                            case 'GET':
                                $response['data'] = $validate->getInfo($id);
                                break;
                            case 'PUT':
                                if ($response['is_me']) {
                                    $validate->data->id = $validate->id;
                                    if ($data['name']) $validate->data->name = $data['name'];
                                    if ($data['pass']) $validate->data->pass = password_hash($data['name'],
                                        PASSWORD_DEFAULT);
                                    if (!empty($data['email'])) {
                                        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                                            $validemail = (new Users())->find(['email' => $data['email']])->fetch();
                                            if (is_null($validemail)) {
                                                $validate->data->email = $data['email'];
                                            } else {
                                                $response['error'] = 'Email Já existente';
                                            }
                                        } else {
                                            $response['error'] = 'Email Inválido';
                                        }
                                    }
                                    $validate->save();
                                    $response['data'] = $validate->getInfo($id);
                                } else {
                                    $response['error'] = 'Não é possível alterar outro Usuário';
                                }
                                break;

                            case 'DELETE':

                                break;

                            default:
                                $response['error'] = "Método de requisição incompatível";
                                break;
                        }
                    } else {
                        $response['error'] = "Usuário não encontrado";
                        $response['data'] = array();
                    }
                } else {
                $response['error'] = "Token Inválido";
            }
        } else {
            $response['error'] = "Token não enviado";
        }

        $this->returnJson($response);
    }
}