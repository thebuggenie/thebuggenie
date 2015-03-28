<?php

    namespace thebuggenie\core\entities;

    use thebuggenie\core\framework;

    /**
     * @Table(name="\thebuggenie\core\entities\tables\ListTypes")
     */
    class Category extends Datatype 
    {

        const ITEMTYPE = Datatype::CATEGORY;

        protected $_itemtype = Datatype::CATEGORY;

        public static function loadFixtures(\thebuggenie\core\entities\Scope $scope)
        {
            $categories = array('General', 'Security', 'User interface');
            
            foreach ($categories as $name)
            {
                $category = new \thebuggenie\core\entities\Category();
                $category->setName($name);
                $category->setScope($scope);
                $category->save();
            }
        }

        public function hasAccess()
        {
            return $this->canUserSet(framework\Context::getUser());
        }

    }
