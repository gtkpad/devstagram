<?php

namespace Controllers;

use \Core\Controller;
use Models\Photos;
use \Models\Users;
use Models\Users_following;

class UsersController extends Controller
{

    public function index()
    {
        $this->returnJson(['status' => 0, 'error' => 'Requisição inválida']);
    }

    public function login()
    {
        $response = array('status' => 0, 'error' => '');
        $method = $this->getMethod();
        $data = $this->getRequestData();

        if ($method == 'POST') {

            if (!empty($data['email']) && !empty($data['pass'])) {

                $users = new Users();
                if ($users->checkCredentials($data['email'], $data['pass'])) {

                    $response['status'] = 1;
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
                    if (!empty($data['offset']) && filter_var($data['offset'], FILTER_VALIDATE_INT)) {
                        $offset = intval($data['offset']);
                    }
                    $limit = 10;
                    if (!empty($data['limit']) && filter_var($data['limit'], FILTER_VALIDATE_INT)) {
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
                    if (!empty($data['offset']) && filter_var($data['offset'], FILTER_VALIDATE_INT)) {
                        $offset = intval($data['offset']);
                    }
                    $limit = 10;
                    if (!empty($data['limit']) && filter_var($data['limit'], FILTER_VALIDATE_INT)) {
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

    public function follow($id)
    {
        $response = ['status' => 0, 'error' => ''];
        $method = $this->getMethod();
        $data = $this->getRequestData();
        if (!empty($data['jwt'])) {
            $user = (new Users())->validateJwt($data['jwt']);
            if (!is_null($user)) {
                $validUser = (new Users())->findById($id);
                $response['logged'] = true;
                if ($user->id !== $id) {
                    if (!is_null($validUser)) {
                        switch ($method) {
                            case 'POST':
                                $validate = (new Users_following())->find(['id_user_active' => $user->id, 'id_user_passive' => $id])
                                    ->fetch();
                                if (is_null($validate)) {
                                    $follow = new Users_following();
                                    $follow->data->id_user_active = $user->id;
                                    $follow->data->id_user_passive = $id;
                                    if ($follow->save()) $response['status'] = 1;
                                }
                                break;
                            case 'DELETE':
                                $follow = (new Users_following())
                                    ->delete(['id_user_active' => $user->id, 'id_user_passive' => $id]);
                                $response['status'] = 1;
                                break;
                            default:
                                $response['error'] = "Método de requisição incompatível";
                                break;
                        }
                    } else {
                        $response['error'] = 'Usuário não encontrado';
                    }
                } else {
                    $response['error'] = 'Operação não permitida';
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