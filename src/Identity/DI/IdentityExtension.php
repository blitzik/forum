<?php declare(strict_types=1);

namespace Identity\DI;

use Kdyby\Doctrine\EntityManager;
use Nette\Security\IUserStorage;
use Nette\DI\CompilerExtension;
use Identity\UserStorage;
use Nette\Http\Session;
use Nette\DI\Compiler;

class IdentityExtension extends CompilerExtension
{
    /**
     * Processes configuration data. Intended to be overridden by descendant.
     * @return void
     */
    /*public function loadConfiguration(): void
    {
        $cb = $this->getContainerBuilder();
        Compiler::loadDefinitions($cb, $this->loadFromFile(__DIR__ . '/services.neon'), $this->name);
    }*/


    /**
     * Adjusts DI container before is compiled to PHP class. Intended to be overridden by descendant.
     * @return void
     */
    public function beforeCompile(): void
    {
        $cb = $this->getContainerBuilder();

        $userStorage = $cb->getDefinitionByType(IUserStorage::class);
        $userStorage->setClass(
            UserStorage::class,
            ['@'.$cb->getByType(Session::class), '@'.$cb->getByType(EntityManager::class)]
        );
    }

}