<?php declare(strict_types = 1);

namespace Post\Services\Writers;

use Doctrine\ORM\OptimisticLockException;
use Kdyby\Doctrine\EntityManager;
use Doctrine\DBAL\LockMode;
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

            $post = $this->createTopic($author, $topic, $text);

            $this->em->flush();
            $this->em->commit();

        } catch (\Exception $e) {
            $this->em->rollback();
            $this->em->close();

            throw $e;
        }

        return $post;
    }


    private function createTopic(Account $author, Topic $topic, string $text): Post
    {
        try {
            $currentTopicVersion = $this->em->createQuery(
                'SELECT t.version FROM ' . Topic::class . ' t
                 WHERE t.id = :id'
            )->setParameter('id', $topic->getId())
             ->getSingleScalarResult();

            $this->em->lock($topic, LockMode::OPTIMISTIC, $currentTopicVersion);

            $post = new Post($author, $topic, $text);
            $this->em->persist($post);

        } catch (OptimisticLockException $e) {
            $this->em->detach($topic);

            /** @var Topic $topic */
            $topic = $this->em->find(Topic::class, $topic->getId());
            $post = $this->write($author, $topic, $text);
        }

        return $post;
    }

}