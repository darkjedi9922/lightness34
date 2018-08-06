<?php namespace frame\tools;

	class Debug {
		
		public function __destruct() {
			unset($this->start);
		}
		
		/**
		* Устанавливает начальную точку времени
		*/
		public function setStart() {
			$this->start = microtime(true);
		}
		
		/**
		* Показывает разницу между текущим временем и заданной начальной точкой
		*/
		public function echoTime() {
			$time = microtime(true) - $this->start;
			//echo '<p>Скрипт выполнялся ', $time, ' сек.</p>';
			printf('Скрипт выполнялся %.4F сек.', $time);
		}
		
		/**
		* @var float начальная точка времени
		*/
		private $start = 0;
	}