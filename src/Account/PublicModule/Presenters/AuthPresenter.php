<?php declare(strict_types = 1);

namespace Account\PublicModule\Presenters;

use Common\PublicModule\Presenters\PublicPresenter;
use Account\Components\ISignInControlFactory;

final class AuthPresenter extends PublicPresenter
{
    /**
     * @var ISignInControlFactory
     * @inject
     */
    public $signInControlFactory;


    public function actionSignIn()
    {
        if ($this->user->isLoggedIn()) {
            $this->redirect(':Category:Public:Homepage:default');
        }

        $this->setMetaTitle('Sign In');
    }
    
    
    public function renderSignIn()
    {
    }


    protected function createComponentSignIn()
    {
        $comp = $this->signInControlFactory->create();

        $comp->onSuccessfulSignIn[] = function () {
            if ($this->_backLink !== null) {
                $this->restoreRequest($this->_backLink);
            } else {
                $this->redirect(':Category:Public:Homepage:default');
            }
        };

        return $comp;
    }


    /*
     * --------------------
     * ----- SIGN OUT -----
     * --------------------
     */


    public function actionSignOut()
    {
        if ($this->user->isLoggedIn()) {
            $this->user->logout();
        }

        $this->redirect(':Category:Public:Homepage:default');
    }
}
