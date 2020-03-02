<?php


namespace Models;
use \Core\Model;

class Photos_comments extends Model
{
     public function __construct()
     {
         parent::__construct(['id_user', 'id_photo', 'date', 'text']);
     }

    public function getComments($photoId)
    {
        $response = array();
        $sql = "SELECT photos_comments.*, users.name 
                FROM photos_comments 
                LEFT JOIN users ON users.id = photos_comments.id_user 
                WHERE photos_comments.id_photo = :id";
        $sql = $this->pdo->prepare($sql);
        $sql->bindValue(":id", $photoId);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $response = $sql->fetchAll(\PDO::FETCH_ASSOC);
        }

        return $response;
    }
}
