<?php
	namespace System\Logging;

    use Monolog\Logger as BaseLogger;

	class Logger
	{
		private $name;
		private $logs;

		public function __construct()
        {
            $this->logs = [];
		}

        public function addLog($name,BaseLogger $log)
        {
            $this->logs[$name] = $log;
        }

		public function setName($name)
		{
			$this->name = $name;
        }

        public function __call($name,$arguments)
        {
            if(!isset($this->logs[$this->name]))
                throw new \InvalidArgumentException('Log does not exist.');

            return call_user_func_array(array($this->logs[$this->name], $name), $arguments);
        }
	}
?>
