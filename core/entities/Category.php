<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\framework;

    /**
     * @Table(name="\thebuggenie\core\entities\tables\ListTypes")
     */
    class Category extends common\Colorizable
    {

        const ITEMTYPE = Datatype::CATEGORY;

        protected $_itemtype = Datatype::CATEGORY;

        public static function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            $categories = array('General', 'Security', 'User interface');
            $categories['General'] = '#FFFFFF';
            $categories['Security'] = '#C2F533';
            $categories['User interface'] = '#55CC55';

            foreach ($categories as $name => $color)
            {
                $category = new \thebuggenie\core\entities\Category();
                $category->setName($name);
                $category->setColor($name);
                $category->setScope($scope);
                $category->save();
            }
        }

        public function hasAccess()
        {
            return $this->canUserSet(framework\Context::getUser());
        }

    }
