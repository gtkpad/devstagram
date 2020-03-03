<?php


namespace Controllers;
use \Core\Controller;
use Models\Photos;
use Models\Users;

class PhotosController extends Controller
{
    public function index()
    {
        $this->returnJson(['status' => 0, 'error' => 'Requisição inválida']);
    }

    public function random()
    {
        $response = ['status' => 0, 'error' => ''];
        $method = $this->getMethod();
        $data = $this->getRequestData();
        if (!empty($data['jwt'])) {
            $user = (new Users())->validateJwt($data['jwt']);
            if (!is_null($user)) {
                $response['logged'] = true;

                if ($method == 'GET') {
                    $photos = new Photos();

                    $limit = 10;
                    if (!empty($data['limit']) && filter_var($data['limit'], FILTER_VALIDATE_INT)) {
                        $limit = intval($data['limit']);
                    }
                    $exclude = [];
                    if (!empty($data['exclude'])) {
                        $exclude = explode(',', $data['exclude']);
                    }
                    $response['data'] = $photos->getRandom($limit, $exclude);
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

    public function view($id)
    {
        $response = ['status' => 0, 'error' => ''];
        $method = $this->getMethod();
        $data = $this->getRequestData();
        if (!empty($data['jwt'])) {
            $user = (new Users())->validateJwt($data['jwt']);
            if (!is_null($user)) {
                $response['logged'] = true;

                if (filter_var($id, FILTER_VALIDATE_INT)) {
                    if ($method == 'GET') {
                        $valid = (new Photos())->findById($id);
                        if (!is_null($valid)) {
                            $photos = new Photos();
                            $response['data'] = $photos->getPhoto($id);
                        } else {
                            $response['error'] = 'Foto não encontrada';
                        }
                    } else {
                        $response['error'] = "Método de requisição incompatível";
                    }
                } else {
                    $response['error'] = "Parametro inválido";
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