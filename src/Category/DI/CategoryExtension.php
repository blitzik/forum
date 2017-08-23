<?php declare(strict_types = 1);

namespace Category\DI;

use Nette\Application\IPresenterFactory;
use Category\Fixtures\CategoryFixture;
use Kdyby\Doctrine\DI\IEntityProvider;
use blitzik\Fixtures\IFixtureProvider;
use Nette\DI\CompilerExtension;
use Nette\DI\Compiler;

class CategoryExtension extends CompilerExtension implements IEntityProvider, IFixtureProvider
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
           ->addSetup('setMapping', [['Category' => 'Category\\*Module\\Presenters\\*Presenter']]);
    }


    function getEntityMappings(): array
    {
        return ['Category' => __DIR__ . '/..'];
    }


    /**
     * @return array
     */
    public function getDataFixtures(): array
    {
        return [
            __DIR__ . '/../Fixtures' => [
                CategoryFixture::class
            ]
        ];
    }

}