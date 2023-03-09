<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Membre as model;
use \App\Models\Timbre as modelTimbre;
use \App\Models\Favoris as modelFavoris;
use \Core\Validation;
use \Core\CheckSession;

/**
 * Membre controller
 *
 * PHP version 7.0
 */
class Membre extends \Core\Controller
{


    static $directory;
    /**
     * Affichage de la page COMPTE de l'utilisateur
     */
    public function indexAction()
    {
        CheckSession::sessionAuth();

        $membre = model::getMembre($_SESSION['user_id']);
        $mises = modelTimbre::getAllMises($_SESSION['user_id']);
        $favoris = modelFavoris::getAllFavoris($_SESSION['user_id']);
        $timbres = modelTimbre::getTimbres($_SESSION['user_id']);
        View::renderTemplate('Membre/index.html', ['membre' => $membre, 'timbres' => $timbres, 'mises' => $mises, 'favoris' => $favoris]);
    }

    /**
     * Redirection sur la page de création de compte
     */
    public function signUpAction()
    {
        $membres = model::getAll();
        View::renderTemplate('Membre/creation.html');
    }

    /**
     * Redirection sur la page de connexion
     */
    public function loginAction()
    {
        View::renderTemplate('Membre/connexion.html');
    }

    /**
     * Vérifiaction de données saisie et verification de mot de passe
     */
    public function authAction() {
        $validation = new \Core\Validation;
        extract($_POST);
        $validation->name('username')->value($username)->pattern('email')->required()->max(50);
        $validation->name('password')->value($password)->required();

        if($validation->isSuccess()){


            $checkUser = model::checkMembre($_POST);
            if($checkUser){
                header('Location: http://'.$_SERVER['SERVER_NAME'].':8080/projet_web/public/');
            } else {
                View::renderTemplate('Membre/connexion.html', ['errors' => $checkUser]);
            }
        }else{
            $errors = $validation->displayErrors();
            View::renderTemplate('Membre/connexion.html', ['errors' => $errors]);
        }
    }

    /**
     * Verification de données saisie et enregistrement dans la base de données
     */
    public function storeAction() {
        $validation = new Validation;
        extract($_POST);
        $validation->name('nom')->value($nom)->pattern('alpha')->required()->max(45);
        $validation->name('prenom')->value($prenom)->pattern('alpha')->required()->max(45);
        $validation->name('zipCode')->value($zipCode)->pattern('alphanum')->required()->max(8);
        $validation->name('adresse')->value($adresse)->pattern('address')->required()->max(70);
        $validation->name('username')->value($username)->pattern('email')->required()->max(50);
        $validation->name('password')->value($password)->max(20)->min(3);

        $checkMembre = model::checkMembreExist($username);
        if($validation->isSuccess() && $checkMembre == ''){
            $options = [
                'cost' => 10,
            ];
            $_POST['password']= password_hash($_POST['password'], PASSWORD_BCRYPT, $options);
            $userInsert = model::insert($_POST);
            header('Location: http://'.$_SERVER['SERVER_NAME'].':8080/projet_web/public/membre/login');
        } else if($checkMembre != '') {
            $errors = $checkMembre;
            View::renderTemplate('Membre/creation.html', ['errors' => $errors]);
        }
        else{
            $errors = $validation->displayErrors();
            View::renderTemplate('Membre/creation.html', ['errors' => $errors]);
        }
    }

    /**
     * Deconnexion de l'utilisateur
     */
    public function logoutAction() {
        session_destroy();
        header('Location: http://'.$_SERVER['SERVER_NAME'].':8080/projet_web/public/');
    }

    /**
     * Changer le mot de passe utilisateur
     */
    public function motdepasseAction() {
        $data['password'] = file_get_contents('php://input');
        model::updatePassword($data);
    }
}
