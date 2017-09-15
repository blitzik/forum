<?php declare(strict_types = 1);

namespace Post\Facades;

use Post\Services\Writers\IPostWriter;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\ResultSet;
use Post\Queries\PostQuery;
use Nette\SmartObject;
use Account\Account;
use Topic\Topic;
use Post\Post;

class PostFacade
{
    use SmartObject;


    /** @var \Kdyby\Doctrine\EntityRepository */
    private $postRepository;

    /** @var IPostWriter */
    private $postWriter;

    /** @var EntityManager */
    private $em;


    public function __construct(
        EntityManager $entityManager,
        IPostWriter $postWriter
    ) {
        $this->em = $entityManager;

        $this->postRepository = $entityManager->getRepository(Post::class);
        $this->postWriter = $postWriter;
    }


    public function write(Account $author, Topic $topic, string $text): Post
    {
        return $this->postWriter->write($author, $topic, $text);
    }


    public function getPost(PostQuery $query): ?Post
    {
        return $this->postRepository->fetchOne($query);
    }


    public function findPosts(PostQuery $query): ResultSet
    {
        return $this->postRepository->fetch($query);
    }

}