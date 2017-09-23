<?php declare(strict_types = 1);

namespace Topic\Services\Writers;

use Topic\Exceptions\TopicCreationFailedException;
use Kdyby\Doctrine\EntityManager;
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
     * @param array $values
     * @return Topic
     * @throws TopicCreationFailedException
     */
    public function write(Account $author, Category $category, array $values): Topic
    {
        try {
            $this->em->beginTransaction();

            $this->em->createQuery(
                'UPDATE ' . Category::class . ' c
                 SET c.version = c.version + 1
                 WHERE c.id = :id'
            )->execute(['id' => $category->getId()]);

            $this->em->refresh($author);

            $topic = new Topic($values['title'], $author, $category);
            if (array_key_exists('isLocked', $values) and $values['isLocked'] === true) {
                $topic->toggleLock();
            }
            $this->em->persist($topic);

            $post = new Post($author, $topic, $values['text']);
            $this->em->persist($post);

            $this->em->flush();

            $topicUrl = $topic->createUrl();
            $this->em->persist($topicUrl);

            $shortTopicUrl = $topic->createUrl(true);
            $shortTopicUrl->setRedirectTo($topicUrl);
            $this->em->persist($shortTopicUrl);

            $topic->setUrl($topicUrl);

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

}