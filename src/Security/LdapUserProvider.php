<?php

namespace App\Security;

use App\Security\User;
use App\Types\HTTPMethodType;
use App\Security\ApiCallService;
use DateTimeZone;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Ldap\Adapter\ExtLdap\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class LdapUserProvider implements UserProviderInterface
{
    const SUFFIXE_EMAIL = '@orabank.net';

    private $base_dn;
   
    public function __construct(
        private Ldap $ldap,
        private RequestStack $requestStack,
        private ApiCallService $apiCallService,
        string $base_dn
    )
    {
        $dotenv = new Dotenv();
        $dotenv->load(dirname( __DIR__, 2).'/.env');
        $this->ldap = $ldap;
        $this->requestStack = $requestStack;
        $this->base_dn = $base_dn;
    }

    /**
     * Loads the user for the given user identifier (e.g. username or email).
     *
     * This method must throw UserNotFoundException if the user is not found.
     *
     * @throws UserNotFoundException
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // Load a User object from your data source or throw UserNotFoundException.
        // The $identifier argument may not actually be a username:
        // it is whatever value is being returned by the getUserIdentifier()
        // method in your User class.
        $plaintextPassword = $this->requestStack->getCurrentRequest()->get('_password', '');
        return $this->getUserEntityCheckedFromLdap($identifier, $plaintextPassword);
    }

    /**
     * search user against ldap and returns the matching App\Entity\User. The $user entity will be created if not exists.
     * @param string $username
     * @param string $password
     * @return User|object|null
     */
    public function getUserEntityCheckedFromLdap(string $username, string $password)
    {
        try{
            $body = [
                "username" => $username,
                "password" => $password,
                "grant_type" => "password",
                "client_id" => $_ENV['KEYCLOCK_CLIENT_ID']
            ];
            $response = $this->apiCallService->sendAuthRequest(HTTPMethodType::POST, $_ENV['KEYCLOCK_LOGIN_URL'], $body);
            if($response != false && array_key_exists('access_token', $response)){
                $dn = explode("@", $username, 2)[0];
                $username = strpos($username, self::SUFFIXE_EMAIL) !== false ? $username : $username.self::SUFFIXE_EMAIL;
                $this->ldap->bind($username, $password);
                $query = $this->ldap->query($this->base_dn, 'sAMAccountName='.$dn);
                $result = $query->execute();
                $entry = $result->toArray()[0];
                if($entry instanceof Entry){
                    $user = new User();
                    $user->setLastName($entry->getAttribute('sn')[0]);
                    $user->setFirstName($entry->getAttribute('givenName')[0]);
                    $user->setEmail($entry->getAttribute('mail')[0]);
                    $user->setUsername($entry->getAttribute('sAMAccountName')[0]);
                    $user->setAccessToken($response['access_token']);
                    $user->setExpiresIn($response['expires_in']);
                    $user->setRefreshExpiresIn($response['refresh_expires_in']);
                    $user->setRefreshToken($response['refresh_token']);
                    $user->setTokenType($response['token_type']);
                    $user->setNotBeforePolicy($response['not-before-policy']);
                    $user->setSessionState($response['session_state']);
                    $user->setScope($response['scope']);
                    $user->setTokenCreatedAt(time());
                    return $user;
                }
            }
            throw new UserNotFoundException;
        }catch(\Exception $e){
            throw new UserNotFoundException;
        }
    }

    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }
        return $user;
        // Return a User object after making sure its data is "fresh".
        // Or throw a UsernameNotFoundException if the user no longer exists.
        throw new \Exception('TODO: fill in refreshUser() inside '.__FILE__);
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass($class)
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }
}