<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends ResourceController
{
    protected $modelName = UserModel::class;
    protected $format = 'json';

    public function login()
    {
        $json = $this->request->getJSON(true);
        $email = $json['email'] ?? null;
        $password = $json['password'] ?? null;

        if (!$email || !$password) {
            return $this->respond(['status'=>false, 'message'=>'Email dan password diperlukan'], 400);
        }

        $user = $this->model->findByEmail($email);
        if (!$user) {
            return $this->respond(['status'=>false, 'message'=>'User tidak ditemukan'], 401);
        }

        if (!password_verify($password, $user['password'])) {
            return $this->respond(['status'=>false, 'message'=>'Password salah'], 401);
        }

        $now = time();
        $exp = $now + intval(env('app.jwtExpire') ?: 3600);
        $payload = [
            'iat' => $now,
            'exp' => $exp,
            'sub' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name']
        ];

        $key = env('app.jwtSecret');
        $jwt = JWT::encode($payload, $key, 'HS256');

        return $this->respond([
            'status' => true,
            'message' => 'Login berhasil',
            'token' => $jwt,
            'expires_at' => $exp
        ], 200);
    }
}
