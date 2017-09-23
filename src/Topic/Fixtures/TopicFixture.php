<?php declare(strict_types = 1);

namespace Topic\Fixtures;

use blitzik\Authorization\Authorizator\AuthorizationRulesGenerator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use blitzik\Routing\Services\UrlGenerator;
use Category\Fixtures\CategoryFixture;
use Account\Fixtures\AccountFixture;
use blitzik\Authorization\Privilege;
use blitzik\Authorization\Resource;
use Category\Category;
use Account\Account;
use Topic\Topic;
use Post\Post;

class TopicFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->loadDefaultUrls($manager);
        $this->loadDefaultAuthorizatorRules($manager);
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
        $arg = new AuthorizationRulesGenerator($manager);

        $privilegeLock = new Privilege('lock');
        $manager->persist($privilegeLock);
        $this->setReference('privilege_lock', $privilegeLock);

        $privilegePin = new Privilege('pin');
        $manager->persist($privilegePin);
        $this->setReference('privilege_pin', $privilegePin);

        $arg->addResource(new Resource(Topic::RESOURCE_ID))
            ->addDefinition($privilegeLock, $this->getReference('role_moderator'))
            ->addDefinition($privilegePin, $this->getReference('role_moderator'));
    }


    public function loadTestingTopics(ObjectManager $manager)
    {
        $member = $this->getReference('user_member');
        $moderator = $this->getReference('user_moderator');
        $admin = $this->getReference('user_admin');

        $manager->refresh($member);
        $manager->refresh($moderator);
        $manager->refresh($admin);

        $categoryOne = $this->getReference('catg_category_one');
        $categoryTwo = $this->getReference('catg_category_two');

        $manager->refresh($categoryOne);
        $manager->refresh($categoryTwo);

        $topics = [];
        $topics[0] = $this->addTopic('Lorem ipsum dolor sit Amet consecteteur', $member, $categoryOne, $manager);
        $this->addPost($member, $topics[0], 'Lorem ipsum dolor sit Amet consecteteur', $manager);
        $this->addPost($moderator, $topics[0], 'Manor ipsum dolor sit Amet consecteteur', $manager);

        $topics[1] = $this->addTopic('Lorem ipsum dolor sit Amet consecteteur', $moderator, $categoryOne, $manager);
        $this->addPost($moderator, $topics[1], 'Conquar ipsum dolor sit Amet consecteteur', $manager);

        $topics[2] = $this->addTopic('Lorem ipsum dolor sit Amet consecteteur', $member, $categoryTwo, $manager);
        $this->addPost($member, $topics[2], 'Itiriem ipsum dolor sit Amet consecteteur', $manager);

        $topics[3] = $this->addTopic('Lorem ipsum dolor sit Amet consecteteur', $admin, $categoryTwo, $manager);
        $this->addPost($admin, $topics[3], 'Asloriam ipsum dolor sit Amet consecteteur', $manager);

        $manager->flush();

        /** @var Topic $topic */
        foreach ($topics as $topic) {
            $topicUrl = $topic->createUrl();
            $manager->persist($topicUrl);

            $shortUrl = $topic->createUrl(true);
            $shortUrl->setRedirectTo($topicUrl);
            $manager->persist($shortUrl);

            $topic->setUrl($topicUrl);
        }
    }


    private function addTopic(string $title, Account $author, Category $category, ObjectManager $manager): Topic
    {
        $t = new Topic($title, $author, $category);
        $manager->persist($t);

        return $t;
    }


    private function addPost(Account $author, Topic $topic, string $text, ObjectManager $manager): Post
    {
        $p = new Post($author, $topic, $text);
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