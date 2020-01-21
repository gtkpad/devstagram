<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");

require 'config.php';
require 'routers.php';
require __DIR__."/vendor/autoload.php";


/* carrega automáticamente os arquivos das classes usadas nos Controllers, Models e Core */
//spl_autoload_register(function($class){
//    if( file_exists('Controllers/'.$class.'.php') ):
//        require 'Controllers/'.$class.'.php';
//    elseif( file_exists('Models/'.$class.'.php') ):
//        require 'Models/'.$class.'.php';
//    elseif( file_exists('Core/'.$class.'.php') ):
//        require 'Core/'.$class.'.php';
//    endif;
//
//});

$core = new \Core\Core();
$core->run();
?>