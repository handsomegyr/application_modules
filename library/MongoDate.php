<?php
if (!class_exists('MongoDate')) {
	class MongoDate
	{

		public $sec;

		public function __construct($timestamp = 0)
		{
			if (empty($timestamp)) {
				$timestamp = time();
			}
			$this->sec = $timestamp;
		}
	}
}