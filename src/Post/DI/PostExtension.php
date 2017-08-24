<?php declare(strict_types = 1);

namespace Post\DI;

use Nette\Application\IPresenterFactory;
use Kdyby\Doctrine\DI\IEntityProvider;
use blitzik\Fixtures\IFixtureProvider;
use Nette\DI\CompilerExtension;
use Post\Fixtures\PostFixture;
use Nette\DI\Compiler;

class PostExtension extends CompilerExtension implements IEntityProvider, IFixtureProvider
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
           ->addSetup('setMapping', [['Post' => 'Post\\*Module\\Presenters\\*Presenter']]);
    }


    function getEntityMappings(): array
    {
        return ['Post' => __DIR__ . '/..'];
    }


    /**
     * @return array
     */
    public function getDataFixtures(): array
    {
        return [
            __DIR__ . '/../Fixtures' => [
                PostFixture::class // todo
            ]
        ];
    }

}