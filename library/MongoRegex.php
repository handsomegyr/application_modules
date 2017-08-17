<?php
if (!class_exists('MongoRegex')) {
	class MongoRegex
	{

		protected $regex = null;

		public function __construct($pattern, $flags = "")
		{
			$this->regex = new \MongoDB\BSON\Regex($pattern, $flags);
		}

		public function __toString()
		{
			return $this->regex->__toString();
		}
	}
}