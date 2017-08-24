<?php declare(strict_types=1);

namespace Common\Presenters;

use blitzik\Authorization\Authorizator\Authorizator;
use Common\Components\IFlashMessagesControlFactory;
use Common\Components\IMetaTitleControlFactory;
use Common\Components\IPageTitleControlFactory;
use Common\Components\IMetaTagsControlFactory;
use Common\Components\FlashMessagesControl;
use Nittro\Bridges\NittroUI\PresenterUtils;
use Common\Components\MetaTitleControl;
use Common\Components\PageTitleControl;
use Common\Components\MetaTagsControl;
use Nette\Application\UI\Presenter;

abstract class AppPresenter extends Presenter
{
    use PresenterUtils;


    /**
     * @var IFlashMessagesControlFactory
     * @inject
     */
    public $flashMessagesControlFactory;

    /**
     * @var IMetaTitleControlFactory
     * @inject
     */
    public $metaTitleControlFactory;

    /**
     * @var IPageTitleControlFactory
     * @inject
     */
    public $pageTitleControlFactory;

    /**
     * @var IMetaTagsControlFactory
     * @inject
     */
    public $metaTagsControlFactory;

    /**
     * @var Authorizator
     * @inject
     */
    public $authorizator;


    protected function startup()
    {
        parent::startup();

        // Only redraw default snippets if nobody is receiving a signal
        $this->setRedrawDefault($this->getSignal() === NULL);
    }


    protected function beforeRender(): void
    {
        parent::beforeRender();

        $this->template->assetsVersion = '001';
    }


    protected function afterRender()
    {
        parent::afterRender();

        if ($this->isAjax()) {
            // Redraw default snippets if enabled
            $this->redrawDefault();
        } else {
            $this->template->flashSession = $this->exportFlashSession();
        }
    }


    public function sendPayload()
    {
        // Send flash messages in payload
        $this->payload->flashes = $this->exportFlashSession();

        parent::sendPayload();
    }


    protected function createComponentFlashMessages(): FlashMessagesControl
    {
        return $this->flashMessagesControlFactory
                    ->create();
    }


    protected function createComponentMetaTags(): MetaTagsControl
    {
        return $this->metaTagsControlFactory
                    ->create();
    }


    protected function createComponentMetaTitle(): MetaTitleControl
    {
        return $this->metaTitleControlFactory->create();
    }


    protected function createComponentPageTitle(): PageTitleControl
    {
        return $this->pageTitleControlFactory->create();
    }
}