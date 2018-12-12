<?php

    namespace thebuggenie\modules\mailing\entities\tables;

    use b2db\Insertion;
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

        protected function initialize()
        {
            parent::setup(self::B2DBNAME, self::ID);
            parent::addVarchar(self::SUBJECT, 255);
            parent::addVarchar(self::FROM, 255);
            parent::addText(self::TO);
            parent::addText(self::MESSAGE);
            parent::addText(self::MESSAGE_HTML);
            parent::addInteger(self::DATE, 10);
        }

        public function addMailToQueue(Swift_Message $mail)
        {
            $insertion = new Insertion();
            $insertion->add(self::SUBJECT, $mail->getSubject());
            $insertion->add(self::FROM, serialize($mail->getFrom()));
            $insertion->add(self::TO, serialize($mail->getTo()));
            $insertion->add(self::MESSAGE, $mail->getBody());
            $insertion->add(self::MESSAGE_HTML, serialize($mail->getChildren()));
            $insertion->add(self::DATE, NOW);
            $insertion->add(self::SCOPE, framework\Context::getScope()->getID());

            $res = $this->rawInsert($insertion);

            return $res->getInsertID();
        }

        public function getQueuedMessages($limit = null)
        {
            $query = $this->getQuery();
            $query->where(self::SCOPE, framework\Context::getScope()->getID());
            if ($limit !== null)
            {
                $query->setLimit($limit);
            }
            $query->addOrderBy(self::DATE, \b2db\QueryColumnSort::SORT_ASC);

            $messages = array();
            $res = $this->rawSelect($query);

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
            $query = $this->getQuery();
            $query->where(self::ID, (array) $ids, \b2db\Criterion::IN);
            $query->where(self::SCOPE, framework\Context::getScope()->getID());

            $res = $this->rawDelete($query);
        }

    }
