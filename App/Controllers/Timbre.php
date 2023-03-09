<?php

namespace App\Controllers;


use \Core\View;
use \App\Models\Timbre as model;
use \App\Models\Image as modelImage;
use \App\Models\Enchere as modelEnchere;
use \App\Models\Mise as modelMise;


use \Core\Validation;
use \Core\CheckSession;


/**
 * Catalogue controller
 *
 * PHP version 7.0
 */
class Timbre extends \Core\Controller
{
    /**
     * Affichage de timbre aux enchères
     */
    public function indexAction()
    {
        $timbres = model::getAll();
        View::renderTemplate('Timbre/index.html', ['timbres' => $timbres]);
    }

    /**
     * Affichage de détails sur la timbre
     */
    public function showAction()
    {
        $id = $this->route_params['id'];
        $timbre = model::getTimbre($id);
        $mise = modelMise::getMise($id);
        if(gettype($mise) == 'boolean' ) {
            $mise = [
                "prixMise" => 0,
                "Prenom" => '-',
                "Nom" => 'Personne'
            ];
        }
        setlocale(LC_TIME, 'fr_CA');
        $timbre['dateFinName'] = date('d F Y h:i:s' , strtotime($timbre['dateFin']));
        $timbre['dateDebutName'] = date('d F Y h:i:s' , strtotime($timbre['dateDebut']));

        View::renderTemplate('Timbre/show.html', ['timbre' => $timbre, 'mise' => $mise]);
    }

    /**
     * Page création de timbre
     */
    public function createAction() {
        CheckSession::sessionAuth();

        $timbres = model::getAll();
        View::renderTemplate('Timbre/creation.html', ['timbres' => $timbres]);
    }

    /**
     * Enregistrement de timbre dans le système
     */
    public function storeAction() {
        CheckSession::sessionAuth();

        $validation = new Validation;
        $timbre = $_POST;
        $timbre['idTimbre'] = uniqid();
        $timbre['Membre_id'] = $_SESSION['user_id'];

        extract($timbre);
        $validation->name('tirage')->value($tirage)->pattern('int')->required()->max(3);
        $validation->name('etat')->value($etat)->pattern('alpha')->required()->max(10);

        if($validation->isSuccess()){
            model::insert($timbre);


            $url = Timbre::sauvegarderImage();
            $data['url'] = "http://".$_SERVER['SERVER_NAME'].":8080/projet_web/public/Assets/img_Timbres/".$url;
            $data['Timbre_id'] = $idTimbre;
            $data['estPrincip'] = 1;
            modelImage::insert($data);
            header('Location: http://'.$_SERVER['SERVER_NAME'].':8080/projet_web/public/');
        } else {
            $errors = $validation->displayErrors();
            print_r($errors);
            View::renderTemplate('Timbre/creation.html', ['errors' => $errors]);
        }
    }

    /**
     * Page de modification des détails sur la timbre
     */
    public function modifierAction()
    {
        CheckSession::sessionAuth();

        $id = $this->route_params['id'];
        $timbre = model::getTimbre($id);

        setlocale(LC_TIME, 'fr_CA');
        $timbre['dateFinName'] = date('d F Y h:i:s' , strtotime($timbre['dateFin']));
        $timbre['dateDebutName'] = date('d F Y h:i:s' , strtotime($timbre['dateDebut']));
        View::renderTemplate('Timbre/edit.html', ['timbre' => $timbre]);
    }

    /**
     * Enregistrement des modification dans la base de données
     */
    public function editAction()
    {
        CheckSession::sessionAuth();

        $id = $this->route_params['id'];
        $_POST['idTimbre'] = $id;
        print_r($_POST);
        model::save($_POST);
        header("Location: http://".$_SERVER['SERVER_NAME'].":8080/projet_web/public/timbre/show/$id");
    }

    /**
     * Effacer la timbre et l'enchère lié
     */
    public function supprimerAction() {
        CheckSession::sessionAuth();

        $id = $this->route_params['id'];
        modelImage::deleteTimbre($id);
        if (count(modelEnchere::getEncheres($id)) > 1) {
            modelEnchere::deleteTimbre($id);
        }
        $timbres = model::delete($id);
    }

    /**
     * Generation du nom de l'image en enregistrement dans le système
     */
    public static function sauvegarderImage() {

        $info = pathinfo($_FILES['imageFichier']['name']);
        $ext = $info['extension']; // get the extension of the file

        $filename = tempnam('./Assets/img_Timbres', 'ti_');
        rename($filename, $filename .= ".".$ext);
        unlink($filename);
        move_uploaded_file( $_FILES['imageFichier']['tmp_name'], $filename);

        // Nom fichier
        return (explode('\\', $filename)[count(explode('\\', $filename))-1]);
    }


    /**
     * Faire une mise sur la timbre
     */
    public function miserAction() {
        $_POST['Timbre_id'] = $this->route_params['id'];
        $_POST['dateMise'] = date('y-m-d h:i:s');
        $_POST['Membre_id'] = $_SESSION['user_id'];
        modelMise::insert($_POST);
        header("Location: http://".$_SERVER['SERVER_NAME'].":8080/projet_web/public/timbre/show/".$_POST['Timbre_id']);
    }
}
