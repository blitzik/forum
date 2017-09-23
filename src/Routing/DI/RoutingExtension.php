<?php declare(strict_types = 1);

namespace Routing\DI;

use blitzik\Routing\RoutesLoader\DatabaseRoutesLoader;
use blitzik\Routing\RoutesLoader\ForumRoutesLoader;
use Nette\DI\CompilerExtension;

class RoutingExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $cb = $this->getContainerBuilder();

        $forumRouterLoader = $cb->addDefinition($this->prefix('forumRoutesLoader'));
        $forumRouterLoader->setClass(ForumRoutesLoader::class)
                            ->setArguments(['tempDir' => $cb->parameters['tempDir'] . '/routing']);
    }


    public function beforeCompile()
    {
        $cb = $this->getContainerBuilder();

        $cb->removeDefinition($cb->getByType(DatabaseRoutesLoader::class));
    }

}