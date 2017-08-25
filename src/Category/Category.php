<?php declare(strict_types = 1);

namespace Category;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;
use Post\Post;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="category",
 *     indexes={
 *         @Index(name="is_public_position", columns={"is_public", "position"})
 *     }
 * )
 */
class Category
{
    use Identifier;


    const LENGTH_TITLE = 150;
    const LENGTH_DESCRIPTION = 500;


    /**
     * @ORM\ManyToOne(targetEntity="Section")
     * @ORM\JoinColumn(name="section", referencedColumnName="id", nullable=false)
     * @var Section
     */
    private $section;

    /**
     * @ORM\Column(name="title", type="string", length=150, nullable=false, unique=false)
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(name="description", type="string", length=500, nullable=true, unique=false)
     * @var string
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="\Post\Post")
     * @ORM\JoinColumn(name="last_post", referencedColumnName="id", nullable=true)
     * @var \Post\Post
     */
    private $lastPost;

    /**
     * @ORM\Column(name="position", type="integer", nullable=false, unique=false)
     * @var int
     */
    private $position;

    /**
     * @ORM\Column(name="is_public", type="boolean", nullable=false, unique=false)
     * @var bool
     */
    private $isPublic;

    /**
     * @ORM\Column(name="number_of_topics", type="integer", nullable=false, unique=false)
     * @var int
     */
    private $numberOfTopics;

     
    public function __construct(
        string $title,
        Section $section
    ) {
        $this->setTitle($title);
        $this->numberOfTopics = 0;
        $this->section = $section;
        $this->position = 0;
        $this->isPublic = $section->isPublic();
    }


    public function changeLastPost(Post $post): void
    {
        $this->lastPost = $post;
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
        return $this->isPublic;
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


    public function changeSection(Section $panel): void
    {
        $this->section = $panel;
    }


    public function getSectionTitle(): string
    {
        return $this->section->getTitle();
    }


    public function getSectionPosition(): int
    {
        return $this->section->getPosition();
    }


    public function updateTotalNumberOfTopicsBy(int $i): void
    {
        $r = $this->numberOfTopics + $i;
        if ($r < 0) {
            $r = 0;
        }
        $this->numberOfTopics = $r;
    }


    /*
     * -----------------------------
     * ----- LAST POST GETTERS -----
     * -----------------------------
     */


    public function getLastPostAuthorName(): string
    {
        return $this->lastPost->getAuthorName();
    }


    public function getLastPostCreationTime(): \DateTimeImmutable
    {
        return $this->lastPost->getCreationTime();
    }


}