<?php declare(strict_types = 1);

namespace Setting\Fixtures;

use blitzik\Authorization\Authorizator\AuthorizationRulesGenerator;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use blitzik\Routing\Services\UrlGenerator;
use blitzik\Authorization\Resource;
use Setting\Setting;

class SettingFixture extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        //$this->loadDefaultUrls($manager);
        //$this->loadDefaultAuthorizatorRules($manager);
        $this->loadDefaultSetting($manager);

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


    private function loadDefaultSetting(ObjectManager $manager)
    {
        $forumTitle = new Setting('forumTitle', 'Lorem Ipsum');
        $manager->persist($forumTitle);
    }

}