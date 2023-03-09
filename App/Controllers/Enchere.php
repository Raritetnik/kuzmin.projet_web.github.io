<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Enchere as modelEnch;
use \App\Models\Timbre;

use \Core\Validation;
use \Core\CheckSession;

/**
 * Catalogue controller
 *
 * PHP version 7.0
 */
class Enchere extends \Core\Controller
{
    /**
     * Affichage de catalogue des timbres aux enchères
     * Avec la gestion des paramètres de filtres et triage
     */
    public function indexAction()
    {
        $vars = [];
        unset($_GET['catalogue']);
        $valid = new Validation;
        if (isset($_GET['trie'])) {
            $valid->name('trie')->value($_GET['trie'])->pattern('alpha')->required()->max(4);
            $trie = ($valid->isSuccess()) ? $_GET['trie'] : "ASC";
            unset($_GET['trie']);
        } else {
            $trie = 'ASC';
        }

        if(isset($_GET['recherche'])) {
            $timbres = Timbre::recherche($_GET['recherche']);
            $vars['recherche'] = $_GET['recherche'];
        } else if(isset($_GET)) {
            $vars = $_GET;
            $timbres = Timbre::filtrageTimbre($vars, $trie);
        }
        $pays = Timbre::getFiltresPaysTimbre();
        $couleurs = Timbre::getFiltresCouleursTimbre();
        $etats = Timbre::getFiltresEtatTimbre();

        View::renderTemplate('Timbre/catalogue.html', ['timbres' => $timbres, 'pays' => $pays, 'couleurs' => $couleurs, 'etats' => $etats, 'vars' => $vars]);
    }

    /**
     * Redirection sur la page de création de l'enchère
     */
    public function createAction() {
        CheckSession::sessionAuth();

        $timbres = Timbre::getAll();
        View::renderTemplate('Enchere/creation.html', ['timbres' => $timbres]);
    }

    /**
     * Sauvegarde de l'enchère dans la base de données
     */
    public function storeAction() {
        CheckSession::sessionAuth();

        $validation = new Validation;
        extract($_POST);
        $validation->name('prixPlancher')->value($prixPlancher)->pattern('float')->required()->max(15);
        $validation->name('quantiteMise')->value($quantiteMise)->pattern('int')->required()->max(2);

        if($validation->isSuccess()){
            $_POST['Membre_id'] = $_SESSION['user_id'];
            $enchereId = modelEnch::insert($_POST);

            Timbre::updateEnchereDeTimbre($enchereId, $Timbre_id);
            header('Location: http://'.$_SERVER['SERVER_NAME'].':8080/projet_web/public/');
        }else{
            $errors = $validation->displayErrors();
            print_r($errors);
            View::renderTemplate('Enchere/creation.html', ['errors' => $errors]);
        }
    }
}
