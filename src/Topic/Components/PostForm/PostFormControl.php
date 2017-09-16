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
        Account $account = null,
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

        if ($this->account !== null) {
            $template->setFile(__DIR__ . '/postForm.latte');
        } else {
            $template->setFile(__DIR__ . '/notLoggedIn.latte');
        }


        $template->render();
    }
    
    
    protected function createComponentForm()
    {
        $form = new Form;

        $form->addTextArea('text', null)
                ->setRequired('You need to write a reply before submitting form.');

        $form->addSubmit('send', 'Post Reply');

        $form->addProtection();

        $form->onSuccess[] = [$this, 'processReply'];

        return $form;
    }


    public function processReply(Form $form, $values)
    {
        if ($this->account === null) {
            $this->flashMessage('Sign in to post a reply', FlashMessage::WARNING);
            return;
        }

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
     * @param Account|null $account
     * @param Topic $topic
     * @return PostFormControl
     */
    public function create(Account $account = null, Topic $topic): PostFormControl;
}