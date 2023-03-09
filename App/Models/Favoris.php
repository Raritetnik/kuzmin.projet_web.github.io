<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Favoris extends \Core\Model
{

    protected static $fillable = [ 'Membre_id', 'Enchere_id'];

    /**
     * Insertion dans la base de données
     */
    public static function insert($data){
        $pdo = static::getDB();

        $data_keys = array_fill_keys(Favoris::$fillable, '');
        $data_map = array_intersect_key($data, $data_keys);
        $nomChamp = implode(", ",array_keys($data_map));
        $valeurChamp = ":".implode(", :", array_keys($data_map));
        $stmt = $pdo->prepare("INSERT INTO Favoris ($nomChamp) VALUES ($valeurChamp)");

        foreach($data_map as $key=>$value){
            $stmt->bindValue(":$key", $value);
        }
        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
    }

    /**
     * Supprimer l'enchère favoris de la base de données
     */
    public static function delete($id){
        $pdo = static::getDB();
        $stmt = $pdo->prepare("DELETE FROM Favoris
        WHERE Favoris.Enchere_id = :id");

        $stmt->bindValue(":id", $id);
        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
    }

    /**
     * Recupère tous les enchères favoris
     */
    public static function getAllFavoris($id)
    {
        $pdo = static::getDB();
        $stmt = $pdo->prepare("SELECT * FROM Favoris
		INNER JOIN Enchere ON Enchere.idEnchere = Favoris.Enchere_id
        INNER JOIN Timbre ON Timbre.Enchere_id = Enchere.idEnchere
        INNER JOIN Image ON Timbre.idTimbre = Image.Timbre_id
        WHERE Favoris.Membre_id = :id;");

        $stmt->bindValue(':id', $id);

        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
