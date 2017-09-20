<?php declare(strict_types = 1);

namespace Category\Components;

use Common\Components\BaseControl;
use Doctrine\ORM\AbstractQuery;
use Topic\Facades\TopicFacade;
use Topic\Queries\TopicQuery;
use Nette\Utils\ArrayHash;
use Category\Category;

class CategoryTopicsOverviewControl extends BaseControl
{
    /** @var TopicFacade */
    private $topicFacade;


    /** @var bool */
    private $showOnlyPinned = false;

    /** @var bool */
    private $showOnNoTopics = true;

    /** @var Category */
    private $category;

    /** @var string */
    private $title;


    public function __construct(
        Category $category,
        TopicFacade $topicFacade
    ) {
        $this->category = $category;
        $this->topicFacade = $topicFacade;

        $this->title = sprintf('%s # %s', $this->category->getSectionTitle(), $this->category->getTitle());
    }


    public function setTitle(string $title): void
    {
        $this->title = $title;
    }


    public function onlyPinned(): void
    {
        $this->showOnlyPinned = true;
    }


    public function hideOnNoTopics()
    {
        $this->showOnNoTopics = false;
    }


    public function render(): void
    {
        $template = $this->getTemplate();

        $template->category = $this->category;

        $q = new TopicQuery();
        $q->withAuthor(['name'])
          ->withLastPost(['createdAt'])
          ->withLastPostAuthor(['name'])
          ->withLastPostTopic(['id'])
          ->byCategory($this->category);

        if ($this->showOnlyPinned) {
            $q->onlyPinned();
        } else {
            $q->onlyUnpinned();
        }
        $q->orderByDateOfCreation('DESC');

        $topicsResultSet = $this->topicFacade->findTopics($q);
        $topics = $topicsResultSet->toArray(AbstractQuery::HYDRATE_ARRAY);
        $topicsCount = count($topics);

        if ($topicsCount > 0) {
            $showTopics = true;
        } else {
            if ($this->showOnNoTopics) {
                $showTopics = true;
            } else {
                $showTopics = false;
            }
        }

        $template->showTopics = $showTopics;
        $template->title = $this->title;
        if ($topicsCount < 1) {
            $template->setFile(__DIR__ . '/Templates/noTopicsFound.latte');

        } else {
            $template->setFile(__DIR__ . '/Templates/overview.latte');
            $template->topics = ArrayHash::from($topics);
            unset($topics);
        }

        $template->render();
    }
}


interface ICategoryTopicsOverviewControlFactory
{
    /**
     * @param Category $category
     * @return CategoryTopicsOverviewControl
     */
    public function create(Category $category): CategoryTopicsOverviewControl;
}