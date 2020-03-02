<?php

namespace Models;

class Photos extends \Core\Model
{


    public function __construct()
    {
        parent::__construct(['id_user', 'url']);
    }
}