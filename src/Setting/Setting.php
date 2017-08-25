<?php declare(strict_types = 1);

namespace Setting;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;

/**
 * @ORM\Entity
 * @ORM\Table(name="setting")
 *
 */
class Setting
{
    use Identifier;


    const LENGTH_NAME = 100;
    const LENGTH_VALUE = 1000;


    /**
     * @ORM\Column(name="name", type="string", length=100, nullable=false, unique=true)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="value", type="string", length=1000, nullable=false, unique=false)
     * @var string
     */
    private $value;


    public function __construct(
        string $name,
        string $value
    ) {
        $this->setName($name);
        $this->setValue($value);
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


    public function setValue(string $value): void
    {
        Validators::assert($value, sprintf('unicode:1..%s', self::LENGTH_VALUE));
        $this->value = $value;
    }


    public function getValue(): string
    {
        return $this->value;
    }
}