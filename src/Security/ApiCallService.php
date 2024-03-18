<?php 

namespace App\Security;

use \Monolog\Level;
use Monolog\Logger;
use Symfony\Component\Dotenv\Dotenv;
use Monolog\Handler\RotatingFileHandler;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ApiCallService{

    private $clientRequest;

    private $logger;

    public function __construct(
        private TokenStorageInterface $securityToken, 
    )
    {
        $this->securityToken = $securityToken;
        $dotenv = new Dotenv();
        $dotenv->load(dirname( __DIR__, 2).'/.env');
        $this->clientRequest = new CurlHttpClient(['verify_peer' => false, 'verify_host' => false]);
        $this->logger = new Logger('prodeta_api_call');
        $this->logger->pushHandler(new RotatingFileHandler(dirname( __DIR__, 2).'/var/log/api_call.log', 7, Level::Debug));
    }

    public function sendAuthRequest($method, $url, $body){
        $base_api_url = $_ENV['KEYCLOCK_BASE_URL'];
        $timeout = $_ENV['API_CALL_TIMEOUT'];
        $this->clientRequest = $this->clientRequest->withOptions([
            'base_uri' => $base_api_url,
            'headers' => [
                'Content-Type' => 'x-www-form-urlencoded'
            ]
        ]);
        $response = $this->clientRequest->request(
            $method,
            $base_api_url.''.$url,
            [
                'body' => $body,
                'timeout' => $timeout
            ]
        );
        try {
            $data = $response->toArray(false);
            return $data;
        }catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    public function sendRequestToApi($method, $url, $body = null){
        $user = $this->securityToken->getToken()->getUser();
        $token = $user instanceof User ? $user->getAccessToken() : '';
        $base_api_url = $_ENV['BASE_API_URL'];
        $timeout = $_ENV['API_CALL_TIMEOUT'];
        $this->clientRequest = $this->clientRequest->withOptions([
            'base_uri' => $base_api_url,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer ".$token
            ]
        ]);
        $response = $this->clientRequest->request(
            $method,
            $base_api_url.''.$url,
            [
                'body' => json_encode($body),
                'timeout' => $timeout
            ]
        );
        try {
            $statusCode = $response->getStatusCode();
            if($statusCode == '200')
            {
                $data = $response->toArray(false); //everything is ok
            }else{
                $data = []; //something is wrong
            }
            return $data;
        }catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}