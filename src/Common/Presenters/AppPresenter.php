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
use Setting\Facades\SettingFacade;

abstract class AppPresenter extends Presenter
{
    use PresenterUtils;


    /** @persistent */
    public $_backLink = null;



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
     * @var SettingFacade
     * @inject
     */
    public $settingFacade;

    /**
     * @var Authorizator
     * @inject
     */
    public $authorizator;


    /** @var array */
    protected $globalSettings = [];


    protected function startup()
    {
        parent::startup();

        $this->setRedrawDefault($this->getSignal() === NULL);

        $this->redrawControl('flashMessages');
        $this->globalSettings = $this->settingFacade->getAllSettings();

        $this->template->assetsVersion = '001';
        $this->template->flashSession = [];
        $this->template->globalSettings = $this->globalSettings;
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
        if ($this->hasFlashSession()) {
            $flashes = $this->getFlashSession();
            $this->payload->flashes = iterator_to_array($flashes->getIterator());
            $flashes->remove();
        }

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


    protected function setMetaTitle(string $title, bool $projectTitleFirst = true): void
    {
        $this['metaTitle']->setTitle(sprintf('%s%s', $projectTitleFirst ? $this->globalSettings['forumTitle'] . ' - ' : null, $title));
    }
}