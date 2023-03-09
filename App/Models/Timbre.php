<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Timbre extends \Core\Model
{

    protected static $fillable = ['idTimbre','titre', 'dateCreation', 'couleur', 'pays', 'etat', 'tirage', 'dimensions', 'certifier', 'Membre_id'];

    /**
     * Recuperer tous les timbres
     */
    public static function getAll()
    {
        $pdo = static::getDB();
        $stmt = $pdo->query('SELECT * FROM Timbre');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recuperer tous les timbres aux enchères
     */
    public static function getAllwithEnchere()
    {
        $pdo = static::getDB();
        $stmt = $pdo->query('SELECT * FROM Timbre
        INNER JOIN Enchere ON Timbre.Enchere_id = Enchere.idEnchere
        INNER JOIN Image ON Timbre.idTimbre = Image.Timbre_id;');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recuperer tous les mises liée à la timbre
     */
    public static function getAllMises($id)
    {
        $pdo = static::getDB();
        $stmt = $pdo->prepare("SELECT * FROM Timbre
        INNER JOIN Image ON Timbre.idTimbre = Image.Timbre_id
        INNER JOIN Mise ON Mise.Timbre_id = Timbre.idTimbre
        WHERE Mise.Membre_id = :id ORDER BY Mise.prixMise DESC;");

        $stmt->bindValue(':id', $id);

        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recuperer tous les timbres avec les image principales
     */
    public static function getTimbres($id)
    {
        $pdo = static::getDB();
        $stmt = $pdo->prepare("SELECT * FROM Timbre
        INNER JOIN Image ON Image.Timbre_id = Timbre.idTimbre
        WHERE Timbre.Membre_id = :id AND Image.estPrincip = 1");

        $stmt->bindValue(':id', $id);
        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recuperer le timbre spécifique qui est aux enchères
     */
    public static function getTimbre($id)
    {
        $pdo = static::getDB();
        $stmt = $pdo->prepare("SELECT * FROM Timbre
        INNER JOIN Image ON Image.Timbre_id = Timbre.idTimbre
        INNER JOIN Enchere ON Enchere.idEnchere = Timbre.Enchere_id
        WHERE Timbre.idTimbre = :id AND Image.estPrincip = 1");

        $stmt->bindValue(':id', $id);
        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Insertion d'une nouvelle timbre
     */
    public static function insert($data){
        $pdo = static::getDB();

        $data_keys = array_fill_keys(Timbre::$fillable, '');
        $data_map = array_intersect_key($data, $data_keys);
        $nomChamp = implode(", ",array_keys($data_map));
        $valeurChamp = ":".implode(", :", array_keys($data_map));
        $stmt = $pdo->prepare("INSERT INTO timbre ($nomChamp) VALUES ($valeurChamp)");

        foreach($data_map as $key=>$value){
            $stmt->bindValue(":$key", $value);
        }

        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
    }

    /**
     * Page: Mettre à jour le timbre dans la base de données
     */
    public static function updateEnchereDeTimbre($idEnchere, $idTimbre) {
        $pdo = static::getDB();
        $stmt = $pdo->prepare("UPDATE Timbre SET Timbre.Enchere_id = :idEnchere WHERE Timbre.idTimbre = :idTimbre");

        $stmt->bindValue(':idEnchere', $idEnchere);
        $stmt->bindValue(':idTimbre', $idTimbre);

        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
    }


    /**
     * Savegarder des modifications sur le timbre
     */
    public static function save($data) {
        $pdo = static::getDB();
        $stmt = $pdo->prepare("UPDATE Timbre SET `titre` = :titre, `couleur` = :couleur, `pays` = :pays, `dimensions` = :dimensions
        WHERE Timbre.idTimbre = :idTimbre");

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
    }

    /**
     * Supprime le timbre de la base de données
     */
    public static function delete($id){
        $pdo = static::getDB();
        $stmt = $pdo->prepare("DELETE FROM Timbre
        WHERE Timbre.idTimbre = :id");

        $stmt->bindValue(":id", $id);
        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
    }


    // ============================ Filtres et Recherche =========================

    public static function getFiltresCouleursTimbre()
    {
        $pdo = static::getDB();
        $stmt = $pdo->query('SELECT DISTINCT couleur FROM Timbre');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getFiltresEtatTimbre()
    {
        $pdo = static::getDB();
        $stmt = $pdo->query('SELECT DISTINCT etat FROM Timbre');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getFiltresPaysTimbre()
    {
        $pdo = static::getDB();
        $stmt = $pdo->query('SELECT DISTINCT pays FROM Timbre');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     *  Filtrage de l'affichage dans le catalogue des timbres
     *
     * @param mixed $filtres - pays, couleur, etat
     * @param mixed $trie - ASC / DESC
     */
    public static function filtrageTimbre($filtres, $trie)
    {
        $pdo = static::getDB();
        $recherche = '';
        foreach ($filtres as $key => $valeur) {
            if(gettype($filtres) == 'array') {
                $filtres[$key] = "%".$valeur."%";
            } else {
                $filtres->$key = "%" . $valeur . "%";
            }
            $recherche .= "Timbre.$key LIKE :$key AND ";
        }
        $stmt = $pdo->prepare("SELECT * FROM Timbre
        INNER JOIN Image ON Image.Timbre_id = Timbre.idTimbre
        INNER JOIN Enchere ON Enchere.idEnchere = Timbre.Enchere_id
        WHERE ".$recherche." Image.estPrincip = 1
        ORDER BY Timbre.titre $trie;");

        foreach ($filtres as $key => $valeur) {
            $stmt->bindValue(":$key", $valeur);
        }
        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recuperation des données timbres selon la recherche dans la base de données
     */
    public static function recherche($recherche)
    {
        $pdo = static::getDB();
        $recherche = '%'.$recherche.'%';
        $stmt = $pdo->prepare("SELECT * FROM Timbre
        INNER JOIN Image ON Image.Timbre_id = Timbre.idTimbre
        INNER JOIN Enchere ON Enchere.idEnchere = Timbre.Enchere_id
        WHERE Timbre.titre LIKE :recherche
        OR Timbre.couleur LIKE :recherche
        OR Timbre.pays LIKE :recherche
        OR Timbre.etat LIKE :recherche
        OR Timbre.dimensions LIKE :recherche
        AND Image.estPrincip = 1");


        $stmt->bindValue(":recherche", $recherche);
        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
