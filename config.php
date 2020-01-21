<?php
/**
 * Arquivo de configuração do sistema, é necessário alterar as configurações para seu funcionamento
 * também é necessário alterar o arquivo ..htaccess para a url que será utilzada
 */
require 'environment.php';

global $config;

$config = array();

if( ENVIRONMENT == 'development' ):
    define('EMAIL_RECUPERA', '');
    define('BASE_URL', 'http://localhost/mvc/');
    $config['jwt_secret_key'] = '6CC32B2EB26A1BADCCCA83167BD42';
    $config['dbname'] = 'projeto';
    $config['host'] = 'localhost';
    $config['username'] = 'gabriel';
    $config['password'] = 'G@BrI&L';
else:
    define('EMAIL_RECUPERA', '');
    define('BASE_URL', '');
    $config['jwt_secret_key'] = '6CC32B2EB26A1BADCCCA83167BD42';
    $config['dbname'] = '';
    $config['host'] = '';
    $config['username'] = '';
    $config['password'] = '';
endif;

global $pdo;

try{
    $pdo = new PDO("mysql:dbname=".$config['dbname'].";host=".$config['host'].";charset=utf8", $config['username'], $config['password']);
} catch(PDOException $e){
    echo "ERRO: ".$e->getMessage();
    exit;
}


?>
