<?php declare(strict_types = 1);

namespace Post\Facades;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\ResultSet;
use Nette\SmartObject;
use Post\Post;
use Post\Queries\PostQuery;

class PostFacade
{
    use SmartObject;


    /** @var \Kdyby\Doctrine\EntityRepository */
    private $postRepository;

    /** @var EntityManager */
    private $em;


    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;

        $this->postRepository = $entityManager->getRepository(Post::class);
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