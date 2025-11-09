<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['email','password','name'];
    protected $returnType = 'array';
    protected $useTimestamps = true;

    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }
}
