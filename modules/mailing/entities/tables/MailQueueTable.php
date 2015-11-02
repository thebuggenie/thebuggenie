<?php

    namespace thebuggenie\modules\mailing\entities\tables;

    use thebuggenie\core\framework,
        thebuggenie\core\entities\tables\ScopedTable,
        b2db\Criteria,
        Swift_Message;

    /**
     * @Table(name="mailing_queue")
     */
    class MailQueueTable extends ScopedTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'mailing_queue';
        const ID = 'mailing_queue.id';
        const MESSAGE = 'mailing_queue.headers';
        const DATE = 'mailing_queue.date';
        const SCOPE = 'mailing_queue.scope';
        const SUBJECT = 'mailing_queue.subject';
        const FROM = 'mailing_queue.from';
        const TO = 'mailing_queue.to';
        const MESSAGE_HTML = 'mailing_queue.part';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addVarchar(self::SUBJECT, 255);
            parent::_addVarchar(self::FROM, 255);
            parent::_addText(self::TO);
            parent::_addText(self::MESSAGE);
            parent::_addText(self::MESSAGE_HTML);
            parent::_addInteger(self::DATE, 10);
        }

        public function addMailToQueue(Swift_Message $mail)
        {
            $crit = $this->getCriteria();
            $crit->addInsert(self::SUBJECT, $mail->getSubject());
            $crit->addInsert(self::FROM, serialize($mail->getFrom()));
            $crit->addInsert(self::TO, serialize($mail->getTo()));
            $crit->addInsert(self::MESSAGE, $mail->getBody());
            $crit->addInsert(self::MESSAGE_HTML, serialize($mail->getChildren()));
            $crit->addInsert(self::DATE, NOW);
            $crit->addInsert(self::SCOPE, framework\Context::getScope()->getID());

            $res = $this->doInsert($crit);

            return $res->getInsertID();
        }

        public function getQueuedMessages($limit = null)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());
            if ($limit !== null)
            {
                $crit->setLimit($limit);
            }
            $crit->addOrderBy(self::DATE, Criteria::SORT_ASC);

            $messages = array();
            $res = $this->doSelect($crit);

            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    require_once THEBUGGENIE_VENDOR_PATH . 'swiftmailer' . DS . 'swiftmailer' . DS . 'lib' . DS . 'swift_required.php';
                    $message = Swift_Message::newInstance();
                    $message->setSubject($row->get(self::SUBJECT));
                    $message->setFrom(unserialize($row->get(self::FROM)));
                    $message->setTo(unserialize($row->get(self::TO)));
                    $message->setBody($row->get(self::MESSAGE));
                    $message->setChildren(unserialize($row->get(self::MESSAGE_HTML)));

                    $messages[$row->get(self::ID)] = $message;
                }
            }

            return $messages;
        }

        public function deleteProcessedMessages($ids)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ID, (array) $ids, Criteria::DB_IN);
            $crit->addWhere(self::SCOPE, framework\Context::getScope()->getID());

            $res = $this->doDelete($crit);
        }

    }
