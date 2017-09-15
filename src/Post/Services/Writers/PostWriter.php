<?php declare(strict_types = 1);

namespace Post\Services\Writers;

use Category\Category;
use Topic\Exceptions\PostCreationFailedException;
use Forum\Exceptions\OptimisticLockException;
use Kdyby\Doctrine\EntityManager;
use Account\Account;
use Topic\Topic;
use Post\Post;

class PostWriter implements IPostWriter
{
    /** @var EntityManager */
    private $em;


    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    /**
     * @param Account $author
     * @param Topic $topic
     * @param string $text
     * @return Post
     * @throws \Exception
     */
    public function write(Account $author, Topic $topic, string $text): Post
    {
        try {
            $this->em->beginTransaction();

            $this->em->createQuery(
                'UPDATE ' . Category::class . ' c
                 SET c.version = c.version + 1
                 WHERE c.id = :id'
            )->execute(['id' => $topic->getCategoryId()]);

            $this->em->createQuery(
                'UPDATE ' . Topic::class . ' t
                 SET t.version = t.version + 1
                 WHERE t.id = :id'
            )->execute(['id' => $topic->getId()]);

            $this->em->refresh($author);

            $post = new Post($author, $topic, $text);
            $this->em->persist($post);

            $this->em->flush();
            $this->em->commit();

        } catch (\Exception $e) {
            $this->em->rollback();
            $this->em->close();

            throw new PostCreationFailedException;
        }

        return $post;
    }

}