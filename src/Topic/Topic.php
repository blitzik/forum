<?php declare(strict_types = 1);

namespace Topic;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;
use blitzik\Routing\Url;
use Category\Category;
use Account\Account;
use Post\Post;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="topic",
 *     indexes={
 *         @Index(name="category_is_pinned_created_at", columns={"category", "is_pinned", "created_at"})
 *     }
 * )
 */
class Topic
{
    use Identifier;


    const LENGTH_TITLE = 150;


    /**
     * @ORM\Column(name="version", type="integer", nullable=false, unique=false)
     * @var int
     */
    private $version;

    /**
     * @ORM\ManyToOne(targetEntity="\Account\Account")
     * @ORM\JoinColumn(name="author", referencedColumnName="id", nullable=false)
     * @var Account
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="\Category\Category")
     * @ORM\JoinColumn(name="category", referencedColumnName="id", nullable=false)
     * @var \Category\Category
     */
    private $category;
    
    /**
     * @ORM\Column(name="title", type="string", length=150, nullable=false, unique=false)
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false, unique=false)
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * @ORM\Column(name="number_of_posts", type="integer", nullable=false, unique=false)
     * @var int
     */
    private $numberOfPosts;

    /**
     * @ORM\OneToOne(targetEntity="\Post\Post")
     * @ORM\JoinColumn(name="last_post", referencedColumnName="id", nullable=true)
     * @var Post
     */
    private $lastPost;

    /**
     * @ORM\Column(name="is_locked", type="boolean", nullable=false, unique=false)
     * @var bool
     */
    private $isLocked;

    /**
     * @ORM\Column(name="is_pinned", type="boolean", nullable=false, unique=false)
     * @var bool
     */
    private $isPinned;
    
     
    public function __construct(
        string $title,
        Account $author,
        Category $category
    ) {
        $this->version = 1;
        $this->setTitle($title);
        $this->author = $author;
        $this->category = $category;
        $this->category->addTopic($this);
        $this->numberOfPosts = 0;
        $this->createdAt = new \DateTimeImmutable('now');
        $this->isLocked = false;
        $this->isPinned = false;
    }

    public function getVersion(): int
    {
        return $this->version;
    }


    public function addPost(Post $post): void
    {
        $post->changeTopic($this);
        $this->numberOfPosts += 1;
        $this->lastPost = $post;
        $this->category->addPost($post);
    }


    public function removePost(Post $post): void
    {
        $this->numberOfPosts -= 1;
    }


    public function getNumberOfPosts(): int
    {
        return $this->numberOfPosts;
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


    public function changeCategory(Category $category): void
    {
        $this->category = $category;
    }


    public function lock(): void
    {
        $this->isLocked = true;
    }


    public function unlock(): void
    {
        $this->isLocked = false;
    }


    public function isLocked(): bool
    {
        return $this->isLocked;
    }


    public function pin(): void
    {
        $this->isPinned = true;
    }


    public function unpin(): void
    {
        $this->isPinned = false;
    }


    public function isPinned(): bool
    {
        return $this->isPinned;
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


    /*
     * ----------------------------
     * ----- CATEGORY GETTERS -----
     * ----------------------------
     */


    public function getCategoryId(): int
    {
        return $this->category->getId();
    }


    public function getCategoryVersion(): int
    {
        return $this->category->getVersion();
    }


    public function getCategoryTitle(): string
    {
        return $this->category->getTitle();
    }


    public function getSectionTitle(): string
    {
        return $this->category->getSectionTitle();
    }


    // -----


    /**
     * @param bool $short
     * @return Url
     * @throws \Exception
     */
    public function createUrl(bool $short = false): Url
    {
        if ($this->id === null) {
            throw new \Exception('Entity must be persisted first.');
        }

        $url = new Url();
        if ($short === true) {
            $url->setUrlPath((string)$this->getId(), true);
        } else {
            $url->setUrlPath(sprintf('%s-%s', $this->getId(), $this->getTitle()), true);
        }

        $url->setDestination('Topic:Public:PostsOverview', 'default');
        $url->setInternalId((string)$this->getId());

        return $url;
    }
}