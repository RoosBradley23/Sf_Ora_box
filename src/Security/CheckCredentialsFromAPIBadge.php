<?php

namespace App\Security;

use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;

class CheckCredentialsFromAPIBadge implements BadgeInterface{

    private $email;

    private $password;

    public function __construct(
        ?string $email, 
        ?string $password
    )
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function isResolved(): bool
    {
        $resolved = false;
        // $url = '/authentication/loginAdmin2';
        // $data = [
        //     'email' => $this->email,
        //     'password' => $this->password
        // ];
        // $response = $this->apiCallService->sendAuthRequest('POST', $url, $data);
        // if($response[0]){
        //     if(array_key_exists('data', (array)$response[1])){
        //         $response[1]['data'] == true ? $resolved = true : $resolved = false;
        //     }
        // }
        return $resolved;
    }
}