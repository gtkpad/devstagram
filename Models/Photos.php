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
}