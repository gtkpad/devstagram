<?php


namespace Models;
use \Core\Model;

class Photos_likes extends Model
{
    public function __construct()
    {
        parent::__construct(['id_photo', 'id_user']);
    }
}