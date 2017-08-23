<?php declare(strict_types=1);

namespace Account\Services;

use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Kdyby\Doctrine\EntityManager;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Identity\FakeIdentity;
use Nette\Http\IRequest;
use Nette\SmartObject;
use Account\Account;

class UserAuthenticator implements IAuthenticator
{
    use SmartObject;
    
    
    const CACHE_NAMESPACE = 'accounts.authentication';

    /** @var IRequest */
    private $httpRequest;

    /** @var EntityManager  */
    private $entityManager;


    public function __construct(
        EntityManager $entityManager,
        IRequest $httpRequest
    ) {
        $this->httpRequest = $httpRequest;
        $this->entityManager = $entityManager;
    }

    
    /**
     * Performs an authentication against e.g. database.
     * and returns IIdentity on success or throws AuthenticationException
     * @return IIdentity
     * @throws AuthenticationException
     */
    public function authenticate(array $credentials): IIdentity
    {
        list($email, $password) = $credentials;

        $account = $this->getAccount($email);

        if ($account === null) {
            throw new AuthenticationException('Wrong E-mail');
        }

        if (!Passwords::verify($password, $account->getPassword())) {
            throw new AuthenticationException('Wrong password');

        } elseif (Passwords::needsRehash($account->getPassword())) {
            $account->changePassword($password);
        }


        return new FakeIdentity($account->getId(), get_class($account));
    }


    private function getAccount($email): ?Account
    {
        return $this->entityManager->createQuery(
            'SELECT a, role FROM ' . Account::class . ' a
             JOIN a.role role
             WHERE a.email = :email'
        )->setParameter('email', $email)
         ->getOneOrNullResult();
    }
}