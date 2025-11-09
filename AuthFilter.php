<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Config\Services;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getServer('HTTP_AUTHORIZATION') ?? $request->getHeaderLine('Authorization');
        if (!$authHeader) {
            return Services::response()->setJSON(['status'=>false,'message'=>'Authorization header missing'])->setStatusCode(401);
        }

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return Services::response()->setJSON(['status'=>false,'message'=>'Token invalid format'])->setStatusCode(401);
        }

        $token = $matches[1];
        $key = env('app.jwtSecret');

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $request->user = (array) $decoded;
        } catch (\Exception $e) {
            return Services::response()->setJSON(['status'=>false,'message'=>'Token tidak valid: '.$e->getMessage()])->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
