<?php declare(strict_types = 1);

namespace Post;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Nette\Utils\Validators;
use Account\Account;
use Topic\Topic;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="post",
 *     indexes={
 *         @Index(name="topic_created_at", columns={"topic", "created_at"})
 *     }
 * )
 */
class Post
{
    use Identifier;


    /**
     * @ORM\ManyToOne(targetEntity="\Topic\Topic")
     * @ORM\JoinColumn(name="topic", referencedColumnName="id", nullable=false)
     * @var Topic
     */
    private $topic;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Account\Account")
     * @ORM\JoinColumn(name="author", referencedColumnName="id", nullable=false)
     * @var \Account\Account
     */
    private $author;

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
        Account $author,
        Topic $topic,
        string $text
    ) {
        $this->author = $author;
        $this->author->addPost($this);
        $this->changeTopic($topic);
        $this->updateText($text);
        $this->createdAt = new \DateTimeImmutable('now');
    }


    public function changeTopic(Topic $topic): void
    {
        if ($this->topic === null or $this->topic->getId() !== $topic->getId()) {
            if ($this->topic !== null) {
                $this->topic->removePost($this);
            }
            $this->topic = $topic;
            $topic->addPost($this);
        }
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


    public function getCreationTime(): \DateTimeImmutable
    {
        return $this->createdAt;
    }


    /*
     * --------------------------
     * ----- AUTHOR GETTERS -----
     * --------------------------
     */


    public function getAuthorName(): string
    {
        return $this->author->getName();
    }

}