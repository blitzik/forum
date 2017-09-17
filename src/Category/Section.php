<?php declare(strict_types = 1);

namespace Category;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="section",
 *     indexes={
 *         @Index(name="is_public_position", columns={"is_public", "position"})
 *     }
 * )
 */
class Section
{
    use Identifier;


    const LENGTH_TITLE = 150;


    /**
     * @ORM\Column(name="title", type="string", length=150, nullable=false, unique=false)
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(name="is_public", type="boolean", nullable=false, unique=false)
     * @var bool
     */
    private $isPublic;

    /**
     * @ORM\Column(name="position", type="integer", nullable=false, unique=true)
     * @var int
     */
    private $position;


    public function __construct(
        string $title
    ) {
        $this->setTitle($title);
        $this->position = 0;
        $this->isPublic = true;
    }


    public function setAsPrivate(): void
    {
        $this->isPublic = false;
    }


    public function setAsPublic(): void
    {
        $this->isPublic = false;
    }


    public function isPublic(): bool
    {
        return $this->isPublic === true;
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