<?php declare(strict_types = 1);

namespace Account\Components;

use Common\Components\FlashMessages\FlashMessage;
use Nette\Security\AuthenticationException;
use Common\Components\BaseControl;
use Nette\Application\UI\Form;

class SignInControl extends BaseControl
{
    public $onSuccessfulSignIn = [];


    public function render(): void
    {
        $template = $this->getTemplate();
        $template->setFile(__DIR__ . '/signIn.latte');



        $template->render();
    }


    protected function createComponentSignInForm(): Form
    {
        $form = new Form;

        $form->addText('email', 'E-mail address')
             ->setRequired('Type your E-mail address')
             ->addRule(Form::EMAIL, 'E-mail address has wrong format');

        $form->addPassword('password', 'Password')
             ->setRequired('Type your password');

        $form->addSubmit('signIn', 'Sign In');

        $form->onSuccess[] = [$this, 'processCredentials'];

        return $form;
    }


    public function processCredentials(Form $form, $values): void
    {
        try {
            $this->user->login($values['email'], $values['password']);
            $this->user->setExpiration('+14 days');
            $this->onSuccessfulSignIn();

        } catch (AuthenticationException $e) {
            $this->flashMessage('Wrong E-mail address or Password', FlashMessage::ERROR);
        }
    }
}


interface ISignInControlFactory
{
    /**
     * @return SignInControl
     */
    public function create(): SignInControl;
}