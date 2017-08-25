<?php declare(strict_types = 1);

namespace Setting;

use Nette\Application\IPresenterFactory;
use Kdyby\Doctrine\DI\IEntityProvider;
use blitzik\Fixtures\IFixtureProvider;
use Setting\Fixtures\SettingFixture;
use Nette\DI\CompilerExtension;
use Nette\DI\Compiler;

class SettingExtension extends CompilerExtension implements IEntityProvider, IFixtureProvider
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
           ->addSetup('setMapping', [['Setting' => 'Setting\\*Module\\Presenters\\*Presenter']]);
    }


    function getEntityMappings(): array
    {
        return ['Setting' => __DIR__ . '/..'];
    }


    /**
     * @return array
     */
    public function getDataFixtures(): array
    {
        return [
            __DIR__ . '/../Fixtures' => [
                SettingFixture::class // todo
            ]
        ];
    }

}