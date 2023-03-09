<?php

namespace App\Models;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Membre extends \Core\Model
{

    protected static $fillable = ['nom', 'prenom', 'phone', 'adresse', 'zipCode', 'username', 'password'];
    protected static $fillable2 = ['nom', 'prenom', 'phone', 'adresse', 'zipCode', 'username'];

    /**
     * Recupère tous les membres
     */
    public static function getAll()
    {
        $pdo = static::getDB();
        $stmt = $pdo->query('SELECT * FROM Membre');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recupère le membre spécifique selon ID
     */
    public static function getMembre($id)
    {
        $pdo = static::getDB();
        $stmt = $pdo->prepare("SELECT * FROM Membre WHERE idMembre = :id");

        $stmt->bindValue(":id", $id);
        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verification de l'utilisateur, son mot de passe chiffré
     */
    public static function checkMembre($data) {
        extract($data);
        $pdo = static::getDB();
        $stmt = $pdo->prepare('SELECT * FROM Membre WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();

        if($count == 1){
            if(password_verify($password, $user['password'])){

                session_regenerate_id();
                $_SESSION['user_id'] = $user['idMembre'];
                $_SESSION['fingerPrint'] = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);

                return true;

            }else{
               return "<ul><li>Verifier le mot de passe</li></ul>";
            }
        }else{
            return "<ul><li>Le nom d'utilisateur n'exist pas</li></ul>";
        }
    }

    /**
     * Verification si l'utilisateur existe déjà dans le système
     * @param mixed $username - courriel
     * @return string - Erreur
     */
    public static function checkMembreExist($username) {
        $pdo = static::getDB();
        $stmt = $pdo->prepare('SELECT * FROM Membre WHERE username = ?');
        $stmt->execute($username);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();

        return ($count >= 1) ? "<ul><li>Le nom d'utilisateur exist déjà dans le système</li></ul>" : "";
    }

    /**
     * Insertion de nouveau utilisateur
     */
    public static function insert($data){
        $pdo = static::getDB();

        $data_keys = array_fill_keys(Membre::$fillable, '');
        $data_map = array_intersect_key($data, $data_keys);
        $nomChamp = implode(", ",array_keys($data_map));
        $valeurChamp = ":".implode(", :", array_keys($data_map));
        $stmt = $pdo->prepare("INSERT INTO Membre ($nomChamp) VALUES ($valeurChamp)");

        foreach($data_map as $key=>$value){
            $stmt->bindValue(":$key", $value);
        }
        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
    }

    /**
     * Mettre à jour le mot de passe de l'utilisateur dans la base de données
     */
    public static function updatePassword($data){
        $pdo = static::getDB();
        $options = [
            'cost' => 10,
        ];
        $password = password_hash($data['password'], PASSWORD_BCRYPT, $options);
        $id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("UPDATE Membre SET Membre.password = :password WHERE idMembre = :id");

        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':id', $id);
        if(!$stmt->execute()){
            print_r($stmt->errorInfo());
            die();
        }
    }
}
