<?php 

namespace App\Tests\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginCheckTest extends WebTestCase
{
    public function testLoginCheck()
    {
        $client = HttpClient::create();
        
        // Remplacez ces valeurs avec des informations d'identification valides
        $credentials = [
            'username' => 'admin@admin.com',
            'password' => 'admin',
        ];

        // Envoyer la requête POST à /api/login_check
        $response = $client->request('POST', 'http://127.0.0.1:8000/api/login_check', [
            'json' => $credentials,

        ]);

        // Vérifier que la réponse a un code de statut 200
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        // Vérifier que le contenu de la réponse contient le token JWT
        $responseData = $response->toArray();
        $this->assertArrayHasKey('token', $responseData);

        // Vous pouvez également vérifier la validité du token ici
        $jwt = $responseData['token'];
        $this->assertNotEmpty($jwt);
        //echo($jwt);
        // Si nécessaire, décodez le token et vérifiez son contenu
        // $decodedToken = (array) JWT::decode($jwt, new Key('your_secret_key', 'HS256'));
        // $this->assertArrayHasKey('username', $decodedToken);
        // $this->assertEquals('testuser', $decodedToken['username']);
    }
}