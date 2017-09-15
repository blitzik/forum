<?php declare(strict_types = 1);

namespace Category\PublicModule\Presenters;

use Common\PublicModule\Presenters\PublicPresenter;
use Forum\Exceptions\OptimisticLockException;
use Kdyby\Doctrine\EntityManager;
use Category\Category;
use Account\Account;
use Post\Post;
use Topic\Exceptions\TopicCreationFailedException;
use Topic\Topic;
use Tracy\Debugger;

final class TestPresenter extends PublicPresenter
{
    /**
     * @var EntityManager
     * @inject
     */
    public $em;


    public function actionDefault()
    {
        $author = $this->em->find(Account::class, 1);
        $category = $this->em->find(Category::class, 2);

        $this->write($author, $category, 'aaaa', 'eee');

        $this->terminate();
    }


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
            $affectedRows = $this->em->createQuery(
                'UPDATE ' . Category::class . ' c
                 SET c.numberOfTopics = c.numberOfTopics + 1,
                     c.version = c.version + 1
                 WHERE c.id = :id AND c.version = :version'
            )->execute(['id' => $category->getId(), 'version' => $category->getVersion()]);

            if ($affectedRows === 0) {
                throw new OptimisticLockException;
            }

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


    public function renderDefault()
    {

    }


    public function actionTest()
    {

    }


    public function renderTest()
    {

    }
}