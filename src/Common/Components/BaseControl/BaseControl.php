<?php declare(strict_types=1);

namespace Common\Components;

use Common\Components\FlashMessages\TFlashMessages;
use Nette\Application\IPresenter;
use Nette\Application\UI\Control;
use Nette\Security\IAuthorizator;

abstract class BaseControl extends Control
{
    use TFlashMessages;


    /** @var  IFlashMessagesControlFactory */
    protected $flashMessagesFactory;

    /** @var IAuthorizator */
    protected $authorizator;

    /** @var \Nette\Security\User */
    protected $user;


    public function setAuthorizator(IAuthorizator $authorizator): void
    {
        $this->authorizator = $authorizator;
    }


    public function setUser(\Nette\Security\User $user): void
    {
        $this->user = $user;
    }


    /**
     * @param IFlashMessagesControlFactory $factory
     */
    public function injectFlashMessagesFactory(IFlashMessagesControlFactory $factory): void
    {
        $this->flashMessagesFactory = $factory;
    }


    protected function createComponentFlashMessages(): FlashMessagesControl
    {
        $comp = $this->flashMessagesFactory->create();

        return $comp;
    }

}