<?php

    namespace thebuggenie\core\framework;

    if (!class_exists('\\thebuggenie\\core\\framework\\Context')) require THEBUGGENIE_CORE_PATH . 'framework/Context.php';
    if (!class_exists('\\thebuggenie\\core\\framework\\Event')) require THEBUGGENIE_CORE_PATH . 'framework/Event.php';
    if (!class_exists('\\thebuggenie\\core\\framework\\Logging')) require THEBUGGENIE_CORE_PATH . 'framework/Logging.php';

    class EventTest extends \PHPUnit_Framework_TestCase
    {

        /**
         * @covers \thebuggenie\core\framework\Event::__construct
         * @covers \thebuggenie\core\framework\Event::createNew
         */
        public function testCreateNew()
        {
            $event = \thebuggenie\core\framework\Event::createNew('modulename', 'identifier', 'subject', array('param1' => 1, 'param2' => 2), array('listitem1', 'listitem2'));

            $this->assertInstanceOf('\thebuggenie\core\framework\Event', $event);

            return $event;
        }

        /**
         * @covers \thebuggenie\core\framework\Event::getIdentifier
         * @depends testCreateNew
         */
        public function testGetIdentifier(\thebuggenie\core\framework\Event $event)
        {
            $this->assertEquals('identifier', $event->getIdentifier());
        }

        /**
         * @covers \thebuggenie\core\framework\Event::getModule
         * @depends testCreateNew
         */
        public function testGetModule(\thebuggenie\core\framework\Event $event)
        {
            $this->assertEquals('modulename', $event->getModule());
        }

        /**
         * @covers \thebuggenie\core\framework\Event::getSubject
         * @depends testCreateNew
         */
        public function testGetSubject(\thebuggenie\core\framework\Event $event)
        {
            $this->assertEquals('subject', $event->getSubject());
        }

        /**
         * @covers \thebuggenie\core\framework\Event::getParameters
         * @covers \thebuggenie\core\framework\Event::getParameter
         * @depends testCreateNew
         */
        public function testParameters(\thebuggenie\core\framework\Event $event)
        {
            $this->assertArrayHasKey('param1', $event->getParameters());
            $this->assertEquals(1, $event->getParameter('param1'));
            $this->assertArrayHasKey('param2', $event->getParameters());
            $this->assertEquals(2, $event->getParameter('param2'));
        }

        /**
         * @covers \thebuggenie\core\framework\Event::getReturnList
         * @covers \thebuggenie\core\framework\Event::addToReturnList
         * @covers \thebuggenie\core\framework\Event::setReturnValue
         * @covers \thebuggenie\core\framework\Event::getReturnValue
         * @depends testCreateNew
         */
        public function testReturnListAndReturnValue(\thebuggenie\core\framework\Event $event)
        {
            $this->assertArrayHasKey(0, $event->getReturnList());
            $this->assertContains('listitem1', $event->getReturnList());
            $this->assertArrayHasKey(1, $event->getReturnList());
            $this->assertContains('listitem2', $event->getReturnList());

            $event->addToReturnList('listitem3');
            $this->assertContains('listitem3', $event->getReturnList());

            $event->setReturnValue('fubar');
            $this->assertEquals('fubar', $event->getReturnValue());

            $event->setReturnValue(null);
            $this->assertEquals(null, $event->getReturnValue());
        }

        /**
         * @covers \thebuggenie\core\framework\Event::setProcessed
         * @covers \thebuggenie\core\framework\Event::isProcessed
         * @depends testCreateNew
         */
        public function testProcessEvent(\thebuggenie\core\framework\Event $event)
        {
            $event->setProcessed(true);
            $this->assertTrue($event->isProcessed());
            $event->setProcessed(false);
            $this->assertFalse($event->isProcessed());
        }

        public function listenerCallback(\thebuggenie\core\framework\Event $event)
        {
            $this->wastriggered = true;
            return true;
        }

        public function listenerCallbackNonProcessingFirst(\thebuggenie\core\framework\Event $event)
        {
            $this->wasprocessed[] = 1;
            return true;
        }

        public function listenerCallbackNonProcessingSecond(\thebuggenie\core\framework\Event $event)
        {
            $this->wasprocessed[] = 2;
            $event->setProcessed();
            return true;
        }

        public function listenerCallbackProcessing(\thebuggenie\core\framework\Event $event)
        {
            $this->wasprocessed[] = 3;
            return true;
        }

        /**
         * @covers \thebuggenie\core\framework\Event::listen
         * @covers \thebuggenie\core\framework\Event::isAnyoneListening
         * @covers \thebuggenie\core\framework\Event::clearListeners
         * @depends testCreateNew
         */
        public function testListening(\thebuggenie\core\framework\Event $event)
        {
            \thebuggenie\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallback'));
            $this->assertTrue(\thebuggenie\core\framework\Event::isAnyoneListening('modulename', 'identifier'));

            \thebuggenie\core\framework\Event::clearListeners('modulename', 'identifier');
            $this->assertFalse(\thebuggenie\core\framework\Event::isAnyoneListening('modulename', 'identifier'));

            \thebuggenie\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallbackNonProcessingFirst'));
            \thebuggenie\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallbackNonProcessingSecond'));
            \thebuggenie\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallbackProcessing'));
            $this->assertTrue(\thebuggenie\core\framework\Event::isAnyoneListening('modulename', 'identifier'));

            return $event;
        }

        /**
         * @covers \thebuggenie\core\framework\Event::listen
         * @covers \thebuggenie\core\framework\Event::trigger
         * @covers \thebuggenie\core\framework\Event::triggerUntilProcessed
         * @depends testListening
         */
        public function testTriggeringAndProcessing(\thebuggenie\core\framework\Event $event)
        {
            $this->wastriggered = false;
            \thebuggenie\core\framework\Event::clearListeners('modulename', 'identifier');
            \thebuggenie\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallback'));

            $event->trigger();
            $this->assertAttributeEquals(true, 'wastriggered', $this);

            \thebuggenie\core\framework\Event::clearListeners('modulename', 'identifier');
            \thebuggenie\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallbackNonProcessingFirst'));
            \thebuggenie\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallbackNonProcessingSecond'));
            \thebuggenie\core\framework\Event::listen('modulename', 'identifier', array($this, 'listenerCallbackProcessing'));

            $this->wasprocessed = array();
            $event->triggerUntilProcessed();

            $this->assertAttributeNotEmpty('wasprocessed', $this);
            $this->assertAttributeContains(1, 'wasprocessed', $this);
            $this->assertAttributeContains(2, 'wasprocessed', $this);
            $this->assertAttributeNotContains(3, 'wasprocessed', $this);
        }

    }
