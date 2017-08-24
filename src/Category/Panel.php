<?php declare(strict_types = 1);

namespace Category;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;

/**
 * @ORM\Entity
 * @ORM\Table(name="panel")
 *
 */
class Panel
{
    use Identifier;


    const LENGTH_TITLE = 150;


    /**
     * @ORM\Column(name="title", type="string", length=150, nullable=false, unique=false)
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(name="position", type="integer", nullable=false, unique=false)
     * @var int
     */
    private $position;


    public function __construct(
        string $title
    ) {
        $this->setTitle($title);
        $this->position = 0;
    }


    public function setTitle(string $title): void
    {
        Validators::assert($title, sprintf('unicode:1..%s', self::LENGTH_TITLE));
        $this->title = $title;
    }


    public function getTitle(): string
    {
        return $this->title;
    }


    public function changePosition(int $position): void
    {
        $this->position = $position;
    }


    public function getPosition(): int
    {
        return $this->position;
    }
}