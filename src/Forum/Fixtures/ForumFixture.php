<?php declare(strict_types = 1);

namespace Forum\Fixtures;

use blitzik\Authorization\Authorizator\AuthorizationRulesGenerator;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use blitzik\Routing\Services\UrlGenerator;
use blitzik\Authorization\Resource;

class ForumFixture extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadDefaultUrls($manager);
        $this->loadDefaultAuthorizatorRules($manager);

        $manager->flush();
    }


    private function loadDefaultUrls(ObjectManager $manager)
    {
        //$ug = new UrlGenerator('', $manager);
        //$ug->addUrl('', '');
    }


    private function loadDefaultAuthorizatorRules(ObjectManager $manager)
    {
        //$arg = new AuthorizationRulesGenerator($manager); // todo
    }

}