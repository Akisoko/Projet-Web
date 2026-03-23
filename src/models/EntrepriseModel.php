<?php

namespace App\models;

use App\Core\Model;

class EntrepriseModel extends Model
{
    protected string $table = "Entreprise";
    protected string $primaryKey = "Id_Entreprise";

    public function createEntreprise(array $data): bool
    {
        return $this->create($data);
    }
}