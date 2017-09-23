<?php declare(strict_types = 1);

namespace Topic\PublicModule\Presenters;

use Common\PublicModule\Presenters\PublicPresenter;
use Topic\Components\IPostsOverviewControlFactory;
use Common\Components\FlashMessages\FlashMessage;
use Topic\Components\IPostFormControlFactory;
use Topic\Components\PostsOverviewControl;
use Topic\Components\PostFormControl;
use Topic\Facades\TopicFacade;
use Topic\Queries\TopicQuery;
use Topic\Topic;
use Post\Post;

final class PostsOverviewPresenter extends PublicPresenter
{
    /**
     * @var IPostsOverviewControlFactory
     * @inject
     */
    public $postsOverviewControlFactory;

    /**
     * @var IPostFormControlFactory
     * @inject
     */
    public $postFormControlFactory;

    /**
     * @var TopicFacade
     * @inject
     */
    public $topicFacade;


    /** @var Topic */
    private $topic;


    public function actionDefault($internalId)
    {
        $this->topic = $this->topicFacade
                            ->getTopic(
                                (new TopicQuery())
                                ->withCategory()
                                ->withCategorySection()
                                ->byId((int)$internalId)
                            );

        $this->setMetaTitle($this->topic->getTitle());
    }


    protected function beforeRender()
	{
        parent::beforeRender();

        $this->template->topic = $this->topic;
    }


    protected function createComponentPostsOverview(): PostsOverviewControl
    {
        $comp = $this->postsOverviewControlFactory->create($this->topic);

        return $comp;
    }


    protected function createComponentPostForm(): PostFormControl
    {
        $comp = $this->postFormControlFactory->create($this->user->getIdentity(), $this->topic);

        $comp->onSuccessfulCreation[] = function (Post $post) {
            $this->redirect(sprintf('this#post-%s', $post->getId()));
        };

        return $comp;
    }


    public function handleToggleLock(): void
    {
        if (!$this->authorizator->isAllowed($this->user, Topic::RESOURCE_ID, 'lock')) {
            $this->flashMessage('Not Enough permission to perform operation', FlashMessage::WARNING);
            $this->redirect('this');
        }

        $wasLocked = $this->topic->isLocked();
        try {
            $this->topicFacade->toggleLock($this->topic->getId());
            $this->flashMessage(sprintf('Topic\'s been successfully %s', $wasLocked ? 'unlocked' : 'locked'), FlashMessage::SUCCESS);

        } catch (\Exception $e) {
            $this->flashMessage(sprintf('An error occurred while %s the topic', $wasLocked ? 'unlocking' : 'locking'), FlashMessage::ERROR);
        }

        $this->redirect('this');
    }


    public function handleTogglePin(): void
    {
        if (!$this->authorizator->isAllowed($this->user, Topic::RESOURCE_ID, 'pin')) {
            $this->flashMessage('Not Enough permission to perform operation', FlashMessage::WARNING);
            $this->redirect('this');
        }

        $wasPinned = $this->topic->isPinned();
        try {
            $this->topicFacade->togglePin($this->topic->getId());
            $this->flashMessage(sprintf('Topic\'s been successfully %s', $wasPinned ? 'unpinned' : 'pinned'), FlashMessage::SUCCESS);

        } catch (\Exception $e) {
            $this->flashMessage(sprintf('An error occurred while %s the topic', $wasPinned ? 'unpinning' : 'pinning'), FlashMessage::ERROR);
        }

        $this->redirect('this');
    }
}