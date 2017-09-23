<?php declare(strict_types = 1);

namespace Topic\Components;

use Topic\Exceptions\TopicCreationFailedException;
use Common\Components\FlashMessages\FlashMessage;
use Common\Components\BaseControl;
use Nette\Application\UI\Form;
use Topic\Facades\TopicFacade;
use Category\Category;
use Account\Account;
use Topic\Topic;

class TopicFormControl extends BaseControl
{
    public $onSuccessfulCreation = [];


    /** @var TopicFacade */
    private $topicFacade;


    /** @var Category */
    private $category;

    /** @var Account */
    private $account;


    public function __construct(
        Account $account,
        Category $category,
        TopicFacade $topicFacade
    ) {
        $this->account = $account;
        $this->category = $category;
        $this->topicFacade = $topicFacade;
    }


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/topicForm.latte');

        $template->displayLockOption = $this->authorizator->isAllowed($this->account, Topic::RESOURCE_ID, 'lock');


        $template->render();
    }


    protected function createComponentForm()
    {
        $form = new Form;

        $form->addText('title', 'Topic title')
                ->setRequired('Title of your topic cannot be blank')
                ->addRule(Form::MAX_LENGTH, 'Title is too long. Title of topic cannot be longer than %d characters', Topic::LENGTH_TITLE);

        $form->addTextArea('text', 'Text', 25, 8)
                ->setRequired('Text cannot be blank');

        if ($this->authorizator->isAllowed($this->account, Topic::RESOURCE_ID, 'lock')) {
            $form->addCheckbox('isLocked', 'Lock topic')
                    ->setDefaultValue(false);
        }


        $form->addSubmit('save', 'Create topic');

        $form->onSuccess[] = [$this, 'processForm'];


        return $form;
    }


    public function processForm(Form $form, $values)
    {
        try {
            $newTopic = $this->topicFacade->write($this->account, $this->category, (array)$values);

            $this->onSuccessfulCreation($newTopic);

        } catch (TopicCreationFailedException $e) {
            $this->flashMessage('An error occurred while creating your Topic.', FlashMessage::ERROR);
            $this->redirect('this');
        }
    }

}


interface ITopicFormControlFactory
{
    /**
     * @param Account $account
     * @param Category $category
     * @return TopicFormControl
     */
    public function create(Account $account, Category $category): TopicFormControl;
}