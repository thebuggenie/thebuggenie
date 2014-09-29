<?php

    namespace thebuggenie\modules\mailing;

    /**
     * Main action components
     */
    class Components extends \TBGActionComponent
    {

        public function componentForgotPasswordPane()
        {
            $this->forgottenintro = \thebuggenie\modules\publish\entities\b2db\Articles::getTable()->getArticleByName('ForgottenPasswordIntro');
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
            $this->project = TBGContext::getCurrentProject();
        }

    }
