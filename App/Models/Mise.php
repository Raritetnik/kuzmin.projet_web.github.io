<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Mise extends \Core\Model
{

    protected static $fillable = ['prixMise', 'dateMise', 'Membre_id', 'Enchere_id', 'Timbre_id'];


    /**
     * Recuperer les mise liées à l'utilisateur
     */
    public static function getMise($id)
    {
        $pdo = static::getDB();
        $stmt = $pdo->prepare("SELECT * FROM Mise
        INNER JOIN Membre ON Mise.Membre_id = Membre.idMembre
        WHERE Mise.Timbre_id = :id ORDER BY prixMise DESC LIMIT 1");
        $stmt->bindValue(":id", $id);

        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Inserer une nouvelle mise
     */
    public static function insert($data){
        $pdo = static::getDB();

        $data_keys = array_fill_keys(Mise::$fillable, '');
        $data_map = array_intersect_key($data, $data_keys);
        $nomChamp = implode(", ",array_keys($data_map));
        $valeurChamp = ":".implode(", :", array_keys($data_map));
        $stmt = $pdo->prepare("INSERT INTO Mise ($nomChamp) VALUES ($valeurChamp)");

        foreach($data_map as $key=>$value){
            $stmt->bindValue(":$key", $value);
        }
        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
    }
}
