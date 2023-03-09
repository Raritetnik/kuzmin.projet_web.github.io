<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Image extends \Core\Model
{

    protected static $fillable = ['url', 'estPrincip', 'Timbre_id'];

    /**
     * Insertion de lien dans la base de données
     */
    public static function insert($data){
        $pdo = static::getDB();

        $data_keys = array_fill_keys(Image::$fillable, '');
        $data_map = array_intersect_key($data, $data_keys);
        $nomChamp = implode(", ",array_keys($data_map));
        $valeurChamp = ":".implode(", :", array_keys($data_map));
        $stmt = $pdo->prepare("INSERT INTO Image ($nomChamp) VALUES ($valeurChamp)");

        foreach($data_map as $key=>$value){
            $stmt->bindValue(":$key", $value);
        }
        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
    }

    /**
     * Supprimer image lié à la timbre
     */
    public static function deleteTimbre($id){
        $pdo = static::getDB();
        $stmt = $pdo->prepare("DELETE FROM Image
        WHERE Image.Timbre_id = :id");

        $stmt->bindValue(":id", $id);
        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
    }
}
