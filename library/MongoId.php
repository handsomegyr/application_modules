<?php
if (! class_exists('MongoId')) {

    class MongoId
    {

        protected $objId = null;

        public function __construct($id = "")
        {
            if (empty($id)) {
                $this->objId = new \MongoDB\BSON\ObjectID();
            } else {
                $this->objId = new \MongoDB\BSON\ObjectID($id);
            }
        }

        public function __toString()
        {
            return $this->objId->__toString();
        }
    }
}