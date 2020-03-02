<?php

namespace Controllers;

use \Core\Controller;
use Models\Photos;
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
        $response = ['status' => 0, 'error' => ''];
        $method = $this->getMethod();
        $data = $this->getRequestData();

        if ($method == 'POST') {
            if (!empty($data['name']) && !empty($data['email']) && !empty($data['pass'])) {
                if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $users = new Users();
                    if ($users->register($data['name'], $data['email'], $data['pass'])) {
                        $response['jwt'] = $users->createJwt();
                        $response['status'] = 1;
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
        $response = ['status' => 0, 'error' => '', 'logged' => false];
        $method = $this->getMethod();
        $data = $this->getRequestData();

        if (!empty($data['jwt'])) {
            $user = (new Users())->validateJwt($data['jwt']);
            if (!is_null($user)) {
                $response['logged'] = true;
                $response['is_me'] = false;
                if ($id === $user->id) $response['is_me'] = true;
                    $validate = (new Users())->findById($id);
                    if (!is_null($validate)) {
                        switch ($method) {
                            case 'GET':
                                $response['data'] = $validate->getInfo($id);
                                $response['status'] = 1;
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
                                    $response['status'] = 1;
                                } else {
                                    $response['error'] = 'Não é possível alterar outro Usuário';
                                }
                                break;

                            case 'DELETE':
                                if ($response['is_me']) {
                                    $requestUser = (new Users())->findById($id);
                                    if (!is_null($requestUser)) {
                                        if ($requestUser->destroy()) {
                                            $response['status'] = 1;
                                        }
                                    } else {
                                        $response['error'] = "Usuário não encontrado";
                                    }
                                } else {
                                    $response['error'] = 'Não é possível excluir outro Usuário';
                                }
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

    public function feed()
    {
        $response = ['status' => 0, 'error' => ''];
        $method = $this->getMethod();
        $data = $this->getRequestData();
        if (!empty($data['jwt'])) {
            $user = (new Users())->validateJwt($data['jwt']);
            if(!is_null($user)) {
                $response['logged'] = true;
                if ($method == 'GET') {
                    $offset = 0;
                    if (!empty($data['offset']) && is_numeric($data['offset'])) {
                        $offset = intval($data['offset']);
                    }
                    $limit = 10;
                    if (!empty($data['limit']) && is_numeric($data['limit'])) {
                        $limit = intval($data['limit']);
                    }
                    $response['data'] = $user->getFeed($offset, $limit);
                } else {
                    $response['error'] = "Método de requisição incompatível";
                }
            } else {
                $response['error'] = "Token Inválido";
            }
        } else {
            $response['error'] = "Token não enviado";
        }
        $this->returnJson($response);
    }

    public function photos($id)
    {
        $response = ['status' => 0, 'error' => ''];
        $method = $this->getMethod();
        $data = $this->getRequestData();
        if (!empty($data['jwt'])) {
            $user = (new Users())->validateJwt($data['jwt']);
            if (!is_null($user)) {
                $response['logged'] = true;
                $response['is_me'] = false;
                if ($user->id === $id) $response['is_me'] = true;

                if ($method == 'GET') {
                    $photos = new Photos();
                    $offset = 0;
                    if (!empty($data['offset']) && is_numeric($data['offset'])) {
                        $offset = intval($data['offset']);
                    }
                    $limit = 10;
                    if (!empty($data['limit']) && is_numeric($data['limit'])) {
                        $limit = intval($data['limit']);
                    }
                    $response['data'] = $photos->getPhotosFromUser($id, $offset, $limit);
                } else {
                    $response['error'] = "Método de requisição incompatível";
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