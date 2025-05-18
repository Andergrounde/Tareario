<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
   
    protected $allowedFields = ['nombre', 'email', 'password', 'fecha_registro', 'es_admin'];


    protected $useTimestamps = false; 
    protected $createdField  = 'fecha_registro'; 
    protected $updatedField  = ''; 

    protected $beforeInsert = ['hashPassword'];
  
    public function getUsuarios()
    {
        return $this->findAll();
    }

   
    public function getUserByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    protected function hashPassword(array $data)
    {
       
        if (!isset($data['data']['password'])) {
            return $data;
        }


        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }
}