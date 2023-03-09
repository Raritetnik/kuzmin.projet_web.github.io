<?php

namespace Core;

use \Core\View;
class CheckSession {

    /**
     * Vérification si l'utilisateur est connecté
     */
    static public function sessionAuth() {
        if(!(isset($_SESSION['fingerPrint']) and $_SESSION['fingerPrint'] === md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']))) {
            View::renderTemplate('Home/index.html');
        };
    }
}

?>