<?php

namespace App\Security;

use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    // private $id;

    private $firstName;

    private $lastName;

    private $email;

    private $userame;

    private $hasTwoFactorAuth;

    private $code2FA;

    private $service;

    private $poste;

    private $roles = [];

    private $accessToken;

    private $expiresIn;

    private $refreshExpiresIn;

    private $refreshToken;

    private $tokenType;

    private $notBeforePolicy;

    private $sessionState;

    private $scope;

    private $tokenCreatedAt;

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRole($role)
    {
        if(in_array($role, $this->getRoles())){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }







    public function completeName(): ?string
    {
        return $this->lastName.' '.$this->firstName;
    }

    // public function getId(): ?int
    // {
    //     return $this->id;
    // }

    // public function setId(?int $id): self
    // {
    //     $this->id = $id;

    //     return $this;
    // }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->userame;
    }

    public function setUsername(?string $userame): self
    {
        $this->userame = $userame;

        return $this;
    }

    public function getHasTwoFactorAuth(): ?bool
    {
        return $this->hasTwoFactorAuth;
    }

    public function setHasTwoFactorAuth(bool $hasTwoFactorAuth): self
    {
        $this->hasTwoFactorAuth = $hasTwoFactorAuth;

        return $this;
    }

    public function getCode2FA(): ?string
    {
        return $this->code2FA;
    }

    public function setCode2FA(?string $code2FA): self
    {
        $this->code2FA = $code2FA;

        return $this;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(?string $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(?string $poste): self
    {
        $this->poste = $poste;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getExpiresIn(): ?string
    {
        return $this->expiresIn;
    }

    public function setExpiresIn(?string $expiresIn): self
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    public function getRefreshExpiresIn(): ?string
    {
        return $this->refreshExpiresIn;
    }

    public function setRefreshExpiresIn(?string $refreshExpiresIn): self
    {
        $this->refreshExpiresIn = $refreshExpiresIn;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }

    public function setTokenType(?string $tokenType): self
    {
        $this->tokenType = $tokenType;

        return $this;
    }

    public function getNotBeforePolicy(): ?string
    {
        return $this->notBeforePolicy;
    }

    public function setNotBeforePolicy(?string $notBeforePolicy): self
    {
        $this->notBeforePolicy = $notBeforePolicy;

        return $this;
    }

    public function getSessionState(): ?string
    {
        return $this->sessionState;
    }

    public function setSessionState(?string $sessionState): self
    {
        $this->sessionState = $sessionState;

        return $this;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(?string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    public function getTokenCreatedAt(): ?string
    {
        return $this->tokenCreatedAt;
    }

    public function setTokenCreatedAt(?string $tokenCreatedAt): self
    {
        $this->tokenCreatedAt = $tokenCreatedAt;

        return $this;
    }
}
