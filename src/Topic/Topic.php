<?php declare(strict_types = 1);

namespace Topic;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;
use Category\Category;
use Post\Post;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="topic",
 *     indexes={
 *         @Index(name="category_created_at", columns={"category", "created_at"})
 *     }
 * )
 */
class Topic
{
    use Identifier;


    const LENGTH_TITLE = 150;


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
     * @ORM\ManyToOne(targetEntity="\Post\Post")
     * @ORM\JoinColumn(name="last_post", referencedColumnName="id", nullable=false)
     * @var \Post\Post
     */
    private $lastPost;
    
     
    public function __construct(
        string $title,
        Category $category,
        Post $post
    ) {
        $this->setTitle($title);
        $this->category = $category;
        $this->category->updateTotalNumberOfTopicsBy(1);
        $this->numberOfPosts = 0;
        $this->changeLastPost($post);
        $this->createdAt = new \DateTimeImmutable('now');
    }


    public function changeLastPost(Post $post): void
    {
        $post->changeTopic($this);
        $this->lastPost = $post;
        $this->category->changeLastPost($post);
    }


    public function updateTotalNumberOfPostsBy(int $i): void
    {
        $r = $this->numberOfPosts + $i;
        if ($r < 0) {
            $r = 0;
        }
        $this->numberOfPosts = $r;
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


    public function getCategoryTitle(): string
    {
        return $this->category->getTitle();
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