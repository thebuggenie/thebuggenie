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

        public function componentAccountSettings_NotificationCategories()
        {
            $category_notification_key = Mailing::NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY;
            $selected_category_notifications = [];
            foreach ($this->categories as $category_id => $category) {
                if ($this->getUser()->getNotificationSetting($category_notification_key . '_' . $category_id, false, 'mailing')->isOn()) {
                    $selected_category_notifications[] = $category_id;
                }
            }
            $this->selected_category_notifications = $selected_category_notifications;
            $this->category_key = $category_notification_key;
        }

        public function componentConfigCreateuserEmail()
        {

        }

        public function componentEditIncomingEmailAccount()
        {
            $this->project = \thebuggenie\core\framework\Context::getCurrentProject();
        }

    }
