<?php
/**
 * Controller responsável pela autenticação de usuários e verificação de permissões
 */

namespace Controllers;

use \Core\Controller;

class AuthController extends Controller {
    
    public function verifySession(){
        $tempo = $_SESSION['tempo'] + 7200; 
        if(time() > $tempo):
            session_destroy();
            header("Location: ".BASE_URL);
            exit();
        else:
            $_SESSION['tempo'] = time();
        endif;
    }

    public function adminAuth(){
        
        if ($_SESSION['auth'] == true):
            $this->verifySession();
            $usuario = new Usuarios();
            $usuario = $usuario->getUserId($_SESSION['usuario']);
            $permissoes = explode(';', $usuario['permissoes']);
            if(in_array('sistema', $permissoes) && in_array('usuarios', $permissoes)):
                return true;
                exit();
            endif;
        endif;

        return false;
        exit();
    }


    public function userAuth(){
        if ($_SESSION['auth'] == true):
            $this->verifySession();
            $usuario = new Usuarios();
            $usuario = $usuario->getUserId($_SESSION['usuario']);
            $permissoes = explode(';', $usuario['permissoes']);
            if(in_array('sistema', $permissoes)):
                return true;
            endif;
        endif;
        return false;
        exit();
    }

    public function isLoggedIn(){
        if ($_SESSION['auth'] == true):
            $this->verifySession();
            header("Location: ".BASE_URL);
        else:
            return false;
        endif;
    }

    public function authLogin($email, $pass){
        if ( !empty($email) && !empty($pass) ):
            
            $user = new Usuarios();
            $user = $user->getAuth($email);
            if ( $user != false ):
                if ( password_verify($pass, $user['senha']) ):
                    $_SESSION['auth'] = true;
                    $_SESSION['usuario'] = $user['id'];
                    $_SESSION['nome_usuario'] = $user['nome'];
                    $_SESSION['permissoes'] = explode(';', $user['permissoes']);
                    $_SESSION['tempo'] = time();
                    return true;
                endif;
            endif;
        endif;
        return false;
    }

}

?>