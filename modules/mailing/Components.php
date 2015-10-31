<?php

    namespace thebuggenie\modules\mailing;

    /**
     * Main action components
     */
    class Components extends \thebuggenie\core\framework\ActionComponent
    {

        public function componentForgotPasswordPane()
        {
            $this->forgottenintro = \thebuggenie\modules\publish\entities\tables\Articles::getTable()->getArticleByName('ForgottenPasswordIntro');
        }

        public function componentForgotPasswordLink()
        {

        }

        public function componentSettings()
        {
        }

        public function componentAccountSettings()
        {

        }

        public function componentConfigCreateuserEmail()
        {

        }

        public function componentEditIncomingEmailAccount()
        {
            $this->project = \thebuggenie\core\framework\Context::getCurrentProject();
        }

    }
