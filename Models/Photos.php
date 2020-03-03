<?php

namespace Models;
use \Models\Users;

class Photos extends \Core\Model
{


    public function __construct()
    {
        parent::__construct(['id_user', 'url']);
    }

    public function getFeedCollection($userIds, $offset, $limit)
    {
        $response = array();
        if (count($userIds) > 0) {
            $sql = "SELECT * FROM photos 
                    WHERE id_user IN (".implode(',', $userIds).") 
                    ORDER BY id DESC 
                    LIMIT {$offset}, {$limit}";
            $sql = $this->pdo->query($sql);
            if ($sql->rowCount() > 0) {
                $response = $sql->fetchAll(\PDO::FETCH_ASSOC);

                foreach ($response as $key => $value) {
                    $user = (new Users())->findById($value['id_user']);
                    $userInfo = $user->getInfo();
                    $response[$key]['name'] = $userInfo['name'];
                    $response[$key]['avatar'] = $userInfo['avatar'];
                    $response[$key]['url'] = BASE_URL.'media/photos/'.$value['url'];
                    $response[$key]['like_count'] = count((new Photos_likes())
                                                        ->find(['id_photo' => $value['id']])
                                                        ->fetch(true));
                    $response[$key]['comments'] = (new Photos_comments())->getComments($value['id']);

                }
            }
        }
        return $response;
    }

    public function getRandom($limit, $excludeData = array())
    {
        $response = array();
        $exclude = [];
        foreach ($excludeData as $key => $value) {
            if (filter_var($value, FILTER_VALIDATE_INT)) {
                $exclude[] = intval($value);
            }
        }
        if (count($exclude) > 0) {
            $sql = "SELECT * FROM photos WHERE id NOT IN (".implode(',', $exclude).") ORDER BY RAND() LIMIT {$limit}";
        } else {
            $sql = "SELECT * FROM photos ORDER BY RAND() LIMIT {$limit}";
        }
        $sql = $this->pdo->prepare($sql);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $response = $sql->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($response as $key => $value) {
                $user = (new Users())->findById($value['id_user']);
                $userInfo = $user->getInfo();
                $response[$key]['url'] = BASE_URL.'media/photos/'.$value['url'];
                $response[$key]['like_count'] = count((new Photos_likes())
                    ->find(['id_photo' => $value['id']])
                    ->fetch(true));
                $response[$key]['comments'] = (new Photos_comments())->getComments($value['id']);

            }
        }
        return $response;
    }

    public function getPhotosFromUser($userId, $offset = 0, $limit = 10)
    {
        $response = array();
        $sql = "SELECT * FROM photos WHERE id_user = :id ORDER BY id DESC LIMIT {$offset}, {$limit}";
        $sql = $this->pdo->prepare($sql);
        $sql->bindValue(":id", $userId);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $response = $sql->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($response as $key => $value) {
                $user = (new Users())->findById($value['id_user']);
                $userInfo = $user->getInfo();
                $response[$key]['url'] = BASE_URL.'media/photos/'.$value['url'];
                $response[$key]['like_count'] = count((new Photos_likes())
                    ->find(['id_photo' => $value['id']])
                    ->fetch(true));
                $response[$key]['comments'] = (new Photos_comments())->getComments($value['id']);

            }
        }
        return $response;
    }

    public function getPhoto($id)
    {
        $response = array();
        $sql = "SELECT * FROM photos WHERE id = :id";
        $sql = $this->pdo->prepare($sql);
        $sql->bindValue(":id", $id);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $response = $sql->fetch(\PDO::FETCH_ASSOC);

            $user = (new Users())->findById($response['id_user']);
            $userInfo = $user->getInfo();
            $response['url'] = BASE_URL.'media/photos/'.$response['url'];
            $response['like_count'] = count((new Photos_likes())
                    ->find(['id_photo' => $response['id']])
                    ->fetch(true));
            $response['comments'] = (new Photos_comments())->getComments($response['id']);


        }
        return $response;
    }
}