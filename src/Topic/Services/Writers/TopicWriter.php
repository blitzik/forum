<?php declare(strict_types = 1);

namespace Topic\Services\Writers;

use Topic\Exceptions\TopicCreationFailedException;
use Doctrine\ORM\OptimisticLockException;
use Kdyby\Doctrine\EntityManager;
use Doctrine\DBAL\LockMode;
use Kdyby\Monolog\Logger;
use Category\Category;
use Account\Account;
use Topic\Topic;
use Post\Post;

class TopicWriter implements ITopicWriter
{
    /** @var EntityManager */
    private $em;

    /** @var Logger */
    private $logger;


    public function __construct(
        EntityManager $em,
        Logger $logger
    ) {
        $this->em = $em;
        $this->logger = $logger->channel('topic-creation');
    }


    /**
     * @param Account $author
     * @param Category $category
     * @param string $title
     * @param string $text
     * @return Topic
     * @throws TopicCreationFailedException
     */
    public function write(Account $author, Category $category, string $title, string $text): Topic
    {
        try {
            $this->em->beginTransaction();

            $topic = $this->createTopic($title, $text, $author, $category);

            $topicUrl = $topic->createUrl();
            $this->em->persist($topicUrl);

            $shortTopicUrl = $topic->createUrl(true);
            $shortTopicUrl->setRedirectTo($topicUrl);
            $this->em->persist($shortTopicUrl);

            $this->em->flush();
            $this->em->commit();

        } catch (\Exception $e) {
            $this->em->rollback();
            $this->em->close();

            $this->logger->addCritical(sprintf('[%s#][%s][%s] - %s', $author->getId(), $author->getName(), $author->getEmail(), $e->getMessage()));

            throw new TopicCreationFailedException();
        }

        return $topic;
    }


    private function createTopic(string $title, string $text, Account $author, Category $category): Topic
    {
        try {
            $currentCategoryVersion = $this->em->createQuery(
                'SELECT c.version FROM ' . Category::class . ' c
                 WHERE c.id = :id'
            )->setParameter('id', $category->getId())
             ->getSingleScalarResult();

            $this->em->lock($category, LockMode::OPTIMISTIC, $currentCategoryVersion);

            $topic = new Topic($title, $author, $category);
            $this->em->persist($topic);

            $post = new Post($author, $topic, $text);
            $this->em->persist($post);

            $this->em->flush();

        } catch (OptimisticLockException $e) {
            $this->em->detach($category);

            /** @var Category $category */
            $category = $this->em->find(Category::class, $category->getId());
            $topic = $this->createTopic($title, $text, $author, $category);
        }

        return $topic;
    }

}