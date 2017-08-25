<?php declare(strict_types = 1);

namespace Topic\Facades;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\ResultSet;
use Topic\Queries\TopicQuery;
use Nette\SmartObject;
use Topic\Topic;

class TopicFacade
{
    use SmartObject;


    /** @var \Kdyby\Doctrine\EntityRepository */
    private $topicRepository;

    /** @var EntityManager */
    private $em;


    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;

        $this->topicRepository = $entityManager->getRepository(Topic::class);
    }


    public function getTopic(TopicQuery $query): ?Topic
    {
        return $this->topicRepository->fetchOne($query);
    }


    public function findTopics(TopicQuery $query): ResultSet
    {
        return $this->topicRepository->fetch($query);
    }

}