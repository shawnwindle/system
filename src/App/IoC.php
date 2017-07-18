<?php
	namespace System\App;

	class IoC
	{
		private $map;
		private $singleton;
		private $objs;
		private $short;

		public function __construct()
		{
			$this->map = array();
			$this->singleton = array();
			$this->objs = array();
			$this->short = array();
		}

		public function register($key,\Closure $resolver,$shortkey='')
		{
			$this->map[$key] = $resolver;
			if($shortkey)
				$this->short[$shortkey] = $key;
		}
		
		public function singleton($key,\Closure $resolver,$shortkey='')
		{
			$this->singleton[$key] = $resolver;
			if($shortkey)
				$this->short[$shortkey] = $key;
		}

        private function expandKey($key)
        {
            if(isset($this->short[$key]))
                return $this->short[$key];
            return $key;
        }

        public function has($key)
        {
            $key = $this->expandKey($key);

            if(isset($this->singleton[$key]) || isset($this->map[$key]))
                return true;

            return false;
        }

		public function resolve($key)
        {
            $key = $this->expandKey($key);

            if(isset($this->singleton[$key]))
            {
                return $this->resolveSingleton($key);
            }

            if(isset($this->map[$key]))
            {
                return $this->resolveObj($key);
            }

            return null;
        }

        private function resolveSingleton($key)
        {
            if(isset($this->objs[$key]))
                return $this->objs[$key];

            $obj = $this->singleton[$key];
            $obj = $obj();

            $this->objs[$key] = $obj;
            return $obj;
        }

        private function resolveObj($key)
        {
            $resolver = $this->map[$key];
            return $resolver();
        }
	}
?>
