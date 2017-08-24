<?php declare(strict_types = 1);

namespace Post;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;
use Category\Category;
use Account\Account;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="post",
 *     indexes={
 *         @Index(name="category_created_at", columns={"category", "created_at"})
 *     }
 * )
 */
class Post
{
    use Identifier;


    /**
     * @ORM\ManyToOne(targetEntity="\Category\Category")
     * @ORM\JoinColumn(name="category", referencedColumnName="id", nullable=false)
     * @var \Category\Category
     */
    private $category;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Account\Account")
     * @ORM\JoinColumn(name="owner", referencedColumnName="id", nullable=false)
     * @var \Account\Account
     */
    private $owner;

    /**
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false, unique=false)
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * @ORM\Column(name="text", type="text", nullable=false, unique=false)
     * @var string
     */
    private $text;
    
     
    public function __construct(
        Account $owner,
        Category $category,
        string $text
    ) {
        $this->owner = $owner;
        $this->category = $category;
        $this->createdAt = new \DateTimeImmutable('now');
    }


    public function updateText(?string $text): void
    {
        Validators::assert($text, 'null|unicode:0..');
        $this->text = $text;
    }


    public function getText(): string
    {
        return $this->text;
    }


    public function getDateOfCreation(): \DateTimeImmutable
    {
        return $this->createdAt;
    }


    public function getCategoryName(): string
    {
        return $this->category->getTitle();
    }


    public function getPanelTitle(): string
    {
        return $this->category->getPanelTitle();
    }
}