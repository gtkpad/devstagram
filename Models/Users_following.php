<?php

namespace Models;

class Users_following extends \Core\Model
{


    public function __construct()
    {
        parent::__construct(['id_user_active', 'id_user_passive']);
    }

    public function getFollowing($id)
    {
        $response = array();
        $sql = "SELECT id_user_passive FROM users_following WHERE id_user_active = :id";
        $sql = $this->pdo->prepare($sql);
        $sql->bindValue(":id", $id);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $data = $sql->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($data as $item) {
                $response[] = intval($item['id_user_passive']);
            }
        }
        return $response;
    }
}