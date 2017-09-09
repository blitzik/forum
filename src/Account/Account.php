<?php declare(strict_types = 1);

namespace Account;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Account\Exceptions\InvalidValueException;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use blitzik\Authorization\Role;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Utils\Validators;
use Nette\Security\IRole;
use Nette\Utils\Random;

/**
 * @ORM\Entity
 * @ORM\Table(name="account")
 *
 */
class Account implements IIdentity
{
    use Identifier;


    const ROLE_GUEST = 'guest';
    const ROLE_MEMBER = 'member';
    const ROLE_MODERATOR = 'moderator';
    const ROLE_ADMIN = 'admin';


    const LENGTH_NAME = 50;
    const LENGTH_EMAIL = 100;
    const LENGTH_TOKEN = 32;


    /**
     * @ORM\Column(name="name", type="string", length=50, nullable=false, unique=true)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="email", type="string", length=100, nullable=false, unique=true)
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(name="password", type="string", length=60, nullable=false, unique=false, options={"fixed": true})
     * @var string
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="blitzik\Authorization\Role")
     * @ORM\JoinColumn(name="role", referencedColumnName="id", nullable=false)
     * @var Role
     */
    private $role;

    /**
     * @ORM\Column(name="token", type="string", length=32, nullable=true, unique=false, options={"fixed": true})
     * @var string
     */
    private $token;

    /**
     * @ORM\Column(name="token_validity", type="datetime_immutable", nullable=true, unique=false)
     * @var \DateTimeImmutable
     */
    private $tokenValidity;

    /**
     * @ORM\Column(name="closed_until", type="date_immutable", nullable=true, unique=false)
     * @var \DateTimeImmutable
     */
    private $closedUntil;
    
    /**
     * @ORM\Column(name="registered", type="datetime_immutable", nullable=false, unique=false)
     * @var \DateTimeImmutable
     */
    private $registered;

    /**
     * @ORM\Column(name="number_of_posts", type="integer", nullable=false, unique=false)
     * @var int
     */
    private $numberOfPosts;
    


    public function __construct(
        string $name,
        string $email,
        string $plainPassword,
        Role $role
    ) {
        $this->setName($name);
        $this->setEmail($email);
        $this->changePassword($plainPassword);
        $this->closedUntil = null;
        $this->registered = new \DateTimeImmutable('now');
        $this->numberOfPosts = 0;

        $this->role = $role;
    }


    public function updateTotalNumberOfPostsBy(int $i): void
    {
        $r = $this->numberOfPosts + $i;
        if ($r < 0) {
            $r = 0;
        }
        $this->numberOfPosts = $r;
    }


    public function deactivate(\DateTimeImmutable $until): void
    {
        if ($until < (\DateTimeImmutable::createFromFormat('Y-m-d', date('Y-m-d')))) {
            throw new InvalidValueException();
        }
        $this->closedUntil = $until;
    }


    public function activate(): void
    {
        $this->closedUntil = null;
    }


    public function isClosed(): bool
    {
        if ($this->closedUntil === null) {
            return false;
        }

        if ($this->closedUntil < (\DateTimeImmutable::createFromFormat('Y-m-d', date('Y-m-d')))) {
            return false;
        }

        return true;
    }


    public function setEmail(string $email): void
    {
        Validators::assert($email, 'email');
        Validators::assert($email, sprintf('unicode:1..%s', self::LENGTH_EMAIL));
        $this->email = $email;
    }


    public function getEmail(): string
    {
        return $this->email;
    }


    public function setName(string $name): void
    {
        Validators::assert($name, sprintf('unicode:1..%s', self::LENGTH_NAME));
        $this->name = $name;
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function changePassword(string $plainPassword): void
    {
        $this->password = Passwords::hash($plainPassword);
        $this->token = null;
        $this->tokenValidity = null;
    }


    public function getPassword(): string
    {
        return $this->password;
    }


    public function createToken(\DateTime $validity): string
    {
        $this->token = Random::generate(self::LENGTH_TOKEN);
        $this->tokenValidity = $validity;

        return $this->token;
    }


    public function getToken(): ?string
    {
        return $this->token;
    }


    public function getTokenValidity(): \DateTimeImmutable
    {
        return $this->tokenValidity;
    }


    // ----- IIdentity


    /**
     * @return IRole[]
     */
    public function getRoles(): array
    {
        return [$this->role];
    }


    // ----- IRole


    function getRoleId(): string
    {
        return $this->role->getName();
    }
}