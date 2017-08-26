<?php declare(strict_types = 1);

namespace Category\Components;

use Common\Components\BaseControl;
use Doctrine\ORM\AbstractQuery;
use Topic\Facades\TopicFacade;
use Topic\Queries\TopicQuery;
use Category\Category;

class TopicsOverviewControl extends BaseControl
{
    /** @var TopicFacade */
    private $topicFacade;


    /** @var Category */
    private $category;


    public function __construct(
        Category $category,
        TopicFacade $topicFacade
    ) {
        $this->category = $category;
        $this->topicFacade = $topicFacade;
    }


    public function render(): void
    {
        $template = $this->getTemplate();

        $template->category = $this->category;

        $q = new TopicQuery();
        $q->byCategory($this->category)
          ->withAuthor(['name'])
          ->withLastPost(['createdAt'])
          ->withLastPostAuthor(['name'])
          ->withLastPostTopic(['id'])
          ->orderByDateOfCreation('DESC');

        $topicsResultSet = $this->topicFacade->findTopics($q);
        $topics = $topicsResultSet->toArray(AbstractQuery::HYDRATE_ARRAY);

        if (empty($topics)) {
            $template->setFile(__DIR__ . '/Templates/noTopicsFound.latte');

        } else {
            $template->setFile(__DIR__ . '/Templates/overview.latte');
            $template->topics = $topics;
        }

        $template->render();
    }
}


interface ITopicsOverviewControlFactory
{
    /**
     * @param Category $category
     * @return TopicsOverviewControl
     */
    public function create(Category $category): TopicsOverviewControl;
}