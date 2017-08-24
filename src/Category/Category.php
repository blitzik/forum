<?php declare(strict_types = 1);

namespace Category;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 *
 */
class Category
{
    use Identifier;


    const LENGTH_TITLE = 150;
    const LENGTH_DESCRIPTION = 300;


    /**
     * @ORM\ManyToOne(targetEntity="Panel")
     * @ORM\JoinColumn(name="panel", referencedColumnName="id", nullable=false)
     * @var Panel
     */
    private $panel;

    /**
     * @ORM\Column(name="title", type="string", length=150, nullable=false, unique=false)
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(name="description", type="string", length=300, nullable=false, unique=false)
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(name="position", type="integer", nullable=false, unique=false)
     * @var int
     */
    private $position;

    /**
     * @ORM\Column(name="number_of_topics", type="integer", nullable=false, unique=false)
     * @var int
     */
    private $numberOfTopics;

     
    public function __construct(
        string $title,
        Panel $panel
    ) {
        $this->setTitle($title);
        $this->numberOfTopics = 0;
        $this->panel = $panel;
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


    public function setDescription(?string $description): void
    {
        Validators::assert($description, sprintf('null|unicode:1..', self::LENGTH_DESCRIPTION));
        $this->description = $description;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }


    public function changePosition(int $position): void
    {
        $this->position = $position;
    }


    public function getPosition(): int
    {
        return $this->position;
    }


    public function changePanel(Panel $panel): void
    {
        $this->panel = $panel;
    }


    public function getPanelTitle(): string
    {
        return $this->panel->getTitle();
    }


    public function updateTotalNumberOfTopicsBy(int $i): void
    {
        $r = $this->numberOfTopics + $i;
        if ($r < 0) {
            $r = 0;
        }
        $this->numberOfTopics = $r;
    }

}