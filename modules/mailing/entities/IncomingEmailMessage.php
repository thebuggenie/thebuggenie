<?php

    namespace thebuggenie\modules\mailing\entities;

    class IncomingEmailMessage
    {

        protected $connection;
        protected $messageNumber;
        protected $bodyHTML = '';
        protected $bodyPlain = '';
        protected $attachments;

        public function __construct($connection, $messageNumber)
        {
            $this->connection = $connection;
            $this->messageNumber = $messageNumber;
        }

        public function fetch()
        {
            $structure = imap_fetchstructure($this->connection, $this->messageNumber);
            if (!$structure)
            {
                return false;
            }
            else
            {
                $this->recurse($structure->parts);
                return true;
            }
        }

        public function recurse($messageParts, $prefix = '', $index = 1, $fullPrefix = true)
        {
            foreach ($messageParts as $part)
            {
                $partNumber = $prefix . $index;

                if ($part->type == 0)
                {
                    if ($part->subtype == 'PLAIN')
                    {
                        $this->bodyPlain .= $this->getPart($partNumber, $part->encoding);
                    }
                    else
                    {
                        $this->bodyHTML .= $this->getPart($partNumber, $part->encoding);
                    }
                }
                elseif ($part->type == 2)
                {
                    $msg = new \thebuggenie\modules\mailing\entities\IncomingEmailMessage($this->connection, $this->messageNumber);
                    $msg->recurse($part->parts, $partNumber . '.', 0, false);
                    $this->attachments[] = array(
                        'type' => $part->type,
                        'subtype' => $part->subtype,
                        'filename' => '',
                        'data' => $msg,
                        'inline' => false,
                    );
                }
                elseif (isset($part->parts))
                {
                    if ($fullPrefix)
                    {
                        $this->recurse($part->parts, $prefix . $index . '.');
                    }
                    else
                    {
                        $this->recurse($part->parts, $prefix);
                    }
                }
                elseif ($part->type > 2)
                {
                    if (isset($part->id))
                    {
                        $id = str_replace(array('<', '>'), '', $part->id);
                        $this->attachments[$id] = array(
                            'type' => $part->type,
                            'subtype' => $part->subtype,
                            'filename' => $this->getFilenameFromPart($part),
                            'data' => $this->getPart($partNumber, $part->encoding),
                            'inline' => true,
                        );
                    }
                    else
                    {
                        $this->attachments[] = array(
                            'type' => $part->type,
                            'subtype' => $part->subtype,
                            'filename' => $this->getFilenameFromPart($part),
                            'data' => $this->getPart($partNumber, $part->encoding),
                            'inline' => false,
                        );
                    }
                }

                $index++;
            }
        }

        function getPart($partNumber, $encoding)
        {
            $data = imap_fetchbody($this->connection, $this->messageNumber, $partNumber);
            switch ($encoding)
            {
                case 0: return $data; // 7BIT
                case 1: return $data; // 8BIT
                case 2: return $data; // BINARY
                case 3: return base64_decode($data); // BASE64
                case 4: return quoted_printable_decode($data); // QUOTED_PRINTABLE
                case 5: return $data; // OTHER
            }
        }

        function getFilenameFromPart($part)
        {
            $filename = '';

            if ($part->ifdparameters)
            {
                foreach ($part->dparameters as $object)
                {
                    if (strtolower($object->attribute) == 'filename')
                    {
                        $filename = $object->value;
                    }
                }
            }

            if (!$filename && $part->ifparameters)
            {
                foreach ($part->parameters as $object)
                {
                    if (strtolower($object->attribute) == 'name')
                    {
                        $filename = $object->value;
                    }
                }
            }

            return $filename;
        }

        public function getBodyHTML()
        {
            return $this->bodyHTML;
        }

        public function getBodyPlain()
        {
            return $this->bodyPlain;
        }

        /**
         * Get message attachments
         *
         * @return array|array
         */
        public function getAttachments()
        {
            return $this->attachments;
        }

        public function hasAttachments()
        {
            return (bool) count($this->attachments);
        }

    }
