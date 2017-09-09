<?php declare(strict_types = 1);

namespace Topic\Fixtures;

use blitzik\Authorization\Authorizator\AuthorizationRulesGenerator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use blitzik\Routing\Services\UrlGenerator;
use Category\Fixtures\CategoryFixture;
use Account\Fixtures\AccountFixture;
use blitzik\Authorization\Resource;
use Topic\Topic;
use Post\Post;

class TopicFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->loadDefaultUrls($manager);
        //$this->loadDefaultAuthorizatorRules($manager);
        $this->loadTestingTopics($manager);

        $manager->flush();
    }


    private function loadDefaultUrls(ObjectManager $manager)
    {
        $ug = new UrlGenerator('Topic:Public:Topic', $manager);
        $ug->addUrl('new-topic', 'new');
    }


    private function loadDefaultAuthorizatorRules(ObjectManager $manager)
    {
        $arg = new AuthorizationRulesGenerator($manager); // todo
    }


    public function loadTestingTopics(ObjectManager $manager)
    {
        $topics = [];
        $topics[0] = $this->addTopic('Lorem ipsum dolor sit Amet consecteteur', 'user_member', 'catg_category_one', $manager);
        $this->addPost('user_member', $topics[0], 'Lorem ipsum dolor sit Amet consecteteur', $manager);
        $this->addPost('user_moderator', $topics[0], 'Manor ipsum dolor sit Amet consecteteur', $manager);

        $topics[1] = $this->addTopic('Lorem ipsum dolor sit Amet consecteteur', 'user_moderator', 'catg_category_one', $manager);
        $this->addPost('user_moderator', $topics[1], 'Conquar ipsum dolor sit Amet consecteteur', $manager);

        $topics[2] = $this->addTopic('Lorem ipsum dolor sit Amet consecteteur', 'user_member', 'catg_category_two', $manager);
        $this->addPost('user_member', $topics[2], 'Itiriem ipsum dolor sit Amet consecteteur', $manager);

        $topics[3] = $this->addTopic('Lorem ipsum dolor sit Amet consecteteur', 'user_admin', 'catg_category_two', $manager);
        $this->addPost('user_admin', $topics[3], 'Asloriam ipsum dolor sit Amet consecteteur', $manager);

        $manager->flush();

        /** @var Topic $topic */
        foreach ($topics as $topic) {
            $topicUrl = $topic->createUrl();
            $manager->persist($topicUrl);

            $shortUrl = $topic->createUrl(true);
            $shortUrl->setRedirectTo($topicUrl);
            $manager->persist($shortUrl);
        }
    }


    private function addTopic(string $title, string $referenceAccountName, string $referenceCategoryName, ObjectManager $manager): Topic
    {
        $t = new Topic($title, $this->getReference($referenceAccountName), $this->getReference($referenceCategoryName));
        $manager->persist($t);

        return $t;
    }


    private function addPost(string $referenceAuthorName, Topic $topic, string $text, ObjectManager $manager): Post
    {
        $p = new Post($this->getReference($referenceAuthorName), $topic, $text);
        $manager->persist($p);

        return $p;
    }


    function getDependencies()
    {
        return [
            AccountFixture::class,
            CategoryFixture::class
        ];
    }


}