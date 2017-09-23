<?php declare(strict_types = 1);

namespace Topic\Facades;

use Topic\Exceptions\TopicCreationFailedException;
use Topic\Exceptions\TopicNotFoundException;
use Topic\Services\Writers\ITopicWriter;
use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\ResultSet;
use Topic\Queries\TopicQuery;
use Category\Category;
use Nette\SmartObject;
use Account\Account;
use Topic\Topic;

class TopicFacade
{
    use SmartObject;


    /** @var \Kdyby\Doctrine\EntityRepository */
    private $topicRepository;

    /** @var ITopicWriter */
    private $topicWriter;

    /** @var EntityManager */
    private $em;


    public function __construct(
        EntityManager $entityManager,
        ITopicWriter $topicWriter
    ) {
        $this->em = $entityManager;

        $this->topicRepository = $entityManager->getRepository(Topic::class);
        $this->topicWriter = $topicWriter;
    }


    public function getTopic(TopicQuery $query): ?Topic
    {
        return $this->topicRepository->fetchOne($query);
    }


    public function findTopics(TopicQuery $query): ResultSet
    {
        return $this->topicRepository->fetch($query);
    }


    /**
     * @return Topic
     * @throws TopicCreationFailedException
     */
    public function write(Account $author, Category $category, array $values): Topic
    {
        return $this->topicWriter->write($author, $category, $values);
    }


    /**
     * @param int $topicId
     * @throws TopicNotFoundException
     */
    public function toggleLock(int $topicId): void
    {
        /** @var Topic $topic */
        $topic = $this->em->find(Topic::class, $topicId);
        if ($topic === null) {
            throw new TopicNotFoundException;
        }

        $topic->toggleLock();

        $this->em->flush();
    }


    public function togglePin(int $topicId): void
    {
        /** @var Topic $topic */
        $topic = $this->em->find(Topic::class, $topicId);
        if ($topic === null) {
            throw new TopicNotFoundException;
        }

        $topic->togglePin();

        $this->em->flush();
    }

}