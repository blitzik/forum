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
use Account\Account;
use Topic\Topic;
use Post\Post;

class TopicFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        //$this->loadDefaultUrls($manager);
        //$this->loadDefaultAuthorizatorRules($manager);
        $this->loadTestingTopics($manager);

        $manager->flush();
    }


    private function loadDefaultUrls(ObjectManager $manager)
    {
        $ug = new UrlGenerator('', $manager); // todo
        $ug->addUrl('', '');
    }


    private function loadDefaultAuthorizatorRules(ObjectManager $manager)
    {
        $arg = new AuthorizationRulesGenerator($manager); // todo
    }


    public function loadTestingTopics(ObjectManager $manager)
    {
        $topics = [];
        $p1 = $this->addPost('user_member', null, 'Lorem ipsum dolor sit Amet consecteteur', $manager);
        $topics[0] = $this->addTopic('Lorem ipsum dolor sit Amet consecteteur', 'user_member', 'catg_category_one', $p1, $manager);
        $this->addPost('user_member', $topics[0], 'Manor ipsum dolor sit Amet consecteteur', $manager);

        $p2 = $this->addPost('user_admin', null, 'Conquar ipsum dolor sit Amet consecteteur', $manager);
        $topics[1] = $this->addTopic('Lorem ipsum dolor sit Amet consecteteur', 'user_moderator', 'catg_category_one', $p2, $manager);

        $p3 = $this->addPost('user_moderator', null, 'Itiriem ipsum dolor sit Amet consecteteur', $manager);
        $topics[2] = $this->addTopic('Lorem ipsum dolor sit Amet consecteteur', 'user_member', 'catg_category_two', $p3, $manager);

        $p4 = $this->addPost('user_member', null, 'Asloriam ipsum dolor sit Amet consecteteur', $manager);
        $topics[3] = $this->addTopic('Lorem ipsum dolor sit Amet consecteteur', 'user_admin', 'catg_category_two', $p4, $manager);

        $manager->flush();

        /** @var Topic $topic */
        foreach ($topics as $topic) {
            $topicUrl = UrlGenerator::create(sprintf('%s-%s', $topic->getId(), $topic->getTitle()), true, 'Topic:Public:Topic', 'default', (string)$topic->getId());
            $manager->persist($topicUrl);

            $shortUrl = UrlGenerator::create(sprintf('%s', $topic->getId()), true, 'Topic:Public:Topic', 'default', (string)$topic->getId());
            $shortUrl->setRedirectTo($topicUrl);
            $manager->persist($shortUrl);
        }
    }


    private function addTopic(string $title, string $referenceAccountName, string $referenceCategoryName, Post $post, ObjectManager $manager): Topic
    {
        $t = new Topic($title, $this->getReference($referenceAccountName), $this->getReference($referenceCategoryName), $post);
        $manager->persist($t);

        return $t;
    }


    private function addPost(string $referenceAuthorName, ?Topic $topic, string $text, ObjectManager $manager): Post
    {
        $p = new Post($this->getReference($referenceAuthorName), $text);
        if ($topic !== null) {
            $p->changeTopic($topic);
        }
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