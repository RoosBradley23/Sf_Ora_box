<?php

namespace App\Security;

use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CredentialsInterface;

class CustomLdapAuthBadge implements CredentialsInterface
{
    private $plaintextPassword;

    private $email;

    private $userProvider;

    public function __construct(
        String $plaintextPassword,
        String $email,
        LdapUserProvider $userProvider
    )
    {
        $this->plaintextPassword = $plaintextPassword;
        $this->email = $email;
        $this->userProvider = $userProvider;
    }

    public function isResolved(): bool
    {
        $resolved = false;
        $user = $this->userProvider->getUserEntityCheckedFromLdap($this->email, $this->plaintextPassword);
        if($user != null){
            $resolved = true;
        }
        return $resolved;
    }
}