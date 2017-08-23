<?php declare(strict_types = 1);

namespace Account\DI;

use Nette\Application\IPresenterFactory;
use blitzik\Fixtures\IFixtureProvider;
use Kdyby\Doctrine\DI\IEntityProvider;
use Account\Fixtures\AccountFixture;
use Nette\DI\CompilerExtension;
use Nette\DI\Compiler;

class AccountExtension extends CompilerExtension implements IEntityProvider, IFixtureProvider
{
    public function loadConfiguration()
    {
        $cb = $this->getContainerBuilder();
        Compiler::loadDefinitions($cb, $this->loadFromFile(__DIR__ . '/services.neon'), $this->name);
    }


    public function beforeCompile()
    {
        $cb = $this->getContainerBuilder();

        $cb->getDefinitionByType(IPresenterFactory::class)
           ->addSetup('setMapping', [['Account' => 'Account\\*Module\\Presenters\\*Presenter']]);
    }


    function getEntityMappings(): array
    {
        return ['Account' => __DIR__ . '/..'];
    }


    public function getDataFixtures(): array
    {
        return [
            __DIR__ . '/../Fixtures' => [
                AccountFixture::class
            ]
        ];
    }

}