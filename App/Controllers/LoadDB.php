<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Timbre;
use \App\Models\Favoris;
use Core\Validation;

/**
 * Controller
 * Execution des tâches dans la base de données avec JavaScript
 */
class LoadDB extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $table = Timbre::getAll();
        $json = json_decode(file_get_contents('php://input'));
        $text = file_get_contents('php://input');
        echo('Input: '.$text);
        echo('<pre>');
        print_r($table);
        echo('</pre>');
    }

    /**
     * Recuperation des données sur les timbres avec les filtres appliqués
     * @return void
     */
    public function FiltreAction()
    {
        $json = json_decode(file_get_contents('php://input'));
        $encheres = Timbre::filtrageTimbre($json, 'ASC');
        echo(json_encode($encheres));
    }

    /**
     * Enregistrement en favoris de l'echère dans la base de données
     * @return void
     */
    public function mettreFavorisAction() {
        $text = file_get_contents('php://input');
        $data = [
            'Membre_id' => $_SESSION['user_id'],
            'Enchere_id' => $text
        ];
        Favoris::insert($data);
        echo ($text);
    }

    /**
     * Recuperation des données sur les timbres par la recherche et triage
     * @return void
     */
    public function getTimbresAction() {
        unset($_GET['LoadDB/getTimbres']);
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
            echo('FFFFFFF');
            $timbres = Timbre::filtrageTimbre($vars, $trie);
        }
        echo (json_encode($timbres));
    }
}
