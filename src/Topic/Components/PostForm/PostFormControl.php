<?php declare(strict_types = 1);

namespace Topic\Components;

use Topic\Exceptions\PostCreationFailedException;
use Common\Components\FlashMessages\FlashMessage;
use Common\Components\BaseControl;
use Nette\Application\UI\Form;
use Post\Facades\PostFacade;
use Account\Account;
use Topic\Topic;

class PostFormControl extends BaseControl
{
    public $onSuccessfulCreation = [];


    /** @var PostFacade */
    private $postFacade;


    /** @var Account */
    private $account;

    /** @var Topic */
    private $topic;


    public function __construct(
        Account $account,
        Topic $topic,
        PostFacade $postFacade
    ) {
        $this->account = $account;
        $this->topic = $topic;
        $this->postFacade = $postFacade;
    }


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/postForm.latte');



        $template->render();
    }
    
    
    protected function createComponentForm()
    {
        $form = new Form;

        $form->addTextArea('text', null)
                ->setRequired('You need to write a message before submitting form.');

        $form->addSubmit('send', 'Post Reply');

        $form->onSuccess[] = [$this, 'processReply'];

        return $form;
    }


    public function processReply(Form $form, $values)
    {
        try {
            $post = $this->postFacade->write($this->account, $this->topic, $values['text']);

            $this->onSuccessfulCreation($post);

        } catch (PostCreationFailedException $e) {
            $this->flashMessage('An error occurred while posting your reply', FlashMessage::ERROR);
        }
    }
}


interface IPostFormControlFactory
{
    /**
     * @param Account $account
     * @param Topic $topic
     * @return PostFormControl
     */
    public function create(Account $account, Topic $topic): PostFormControl;
}