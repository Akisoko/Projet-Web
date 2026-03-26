<?php

namespace App\models;

use App\Core\Model;

class UtilisateurModel extends Model
{
    protected string $table = "Utilisateur";
    protected string $primaryKey = "Id_Utilisateur";

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM Utilisateur WHERE Email = ?"
        );
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
}