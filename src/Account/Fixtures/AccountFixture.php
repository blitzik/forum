<?php declare(strict_types = 1);

namespace Account\Fixtures;

use blitzik\Authorization\Authorizator\AuthorizationRulesGenerator;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use blitzik\Routing\Services\UrlGenerator;
use blitzik\Authorization\Privilege;
use blitzik\Authorization\Resource;
use blitzik\Authorization\Role;
use Account\Account;

class AccountFixture extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        //$this->loadDefaultUrls($manager);
        //$this->loadDefaultAuthorizatorRules($manager);
        $this->loadDefaultPrivileges($manager);
        $this->loadDefaultRoles($manager);
        $this->loadTestingData($manager);

        $manager->flush();
    }


    private function loadDefaultUrls(ObjectManager $manager)
    {
        $ug = new UrlGenerator('', $manager);
        $ug->addUrl('', '');
    }


    private function loadDefaultAuthorizatorRules(ObjectManager $manager)
    {
        $arg = new AuthorizationRulesGenerator($manager); // todo
    }


    private function loadDefaultPrivileges(ObjectManager $manager): void
    {
        $create = new Privilege(Privilege::CREATE);
        $manager->persist($create);
        $this->setReference('privilege_create', $create);

        $edit = new Privilege(Privilege::EDIT);
        $manager->persist($edit);
        $this->setReference('privilege_edit', $edit);

        $remove = new Privilege(Privilege::REMOVE);
        $manager->persist($remove);
        $this->setReference('privilege_remove', $remove);

        $view = new Privilege(Privilege::VIEW);
        $manager->persist($view);
        $this->setReference('privilege_view', $view);
    }


    private function loadDefaultRoles(ObjectManager $objManager): void
    {
        $member = new Role(Account::ROLE_MEMBER);
        $objManager->persist($member);

        $moderator = new Role(Account::ROLE_MODERATOR, $member);
        $objManager->persist($moderator);

        $admin = new Role(Account::ROLE_ADMIN, $moderator);
        $objManager->persist($admin);

        $this->addReference('role_member', $member);
        $this->addReference('role_moderator', $moderator);
        $this->addReference('role_admin', $admin);
    }


    private function loadTestingData(ObjectManager $manager): void
    {
        $member = new Account('Lorem ipsum', 'member@project.cz', 'member', $this->getReference('role_member'));
        $manager->persist($member);
        $this->addReference('user_member', $member);

        $moderator = new Account('Consecteteur Eligendi', 'moderator@project.cz', 'moderator', $this->getReference('role_moderator'));
        $manager->persist($moderator);
        $this->addReference('user_moderator', $moderator);

        $admin = new Account('Dolor Sit Amet', 'admin@project.cz', 'admin', $this->getReference('role_admin'));
        $manager->persist($admin);
        $this->addReference('user_admin', $admin);
    }

}