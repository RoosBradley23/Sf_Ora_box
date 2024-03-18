<?php

namespace App\Security;

use App\Security\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class UserProvider implements UserProviderInterface
{ 
    private $requestStack;

    private $container;

    public function __construct(
        RequestStack $requestStack,
        ContainerInterface $container
    )
    {
        $this->requestStack = $requestStack;
        $this->container = $container;
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
        $user = new User();
        return $user;
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
