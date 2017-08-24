<?php declare(strict_types = 1);

namespace Topic;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;
use Category\Category;

/**
 * @ORM\Entity
 * @ORM\Table(name="topic")
 *
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
    
     
    public function __construct(
        string $title,
        Category $category
    ) {
        $this->setTitle($title);
        $this->category = $category;
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
}