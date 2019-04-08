<?php
	namespace System\App;

    use System\App\MVC\View;

	class Messages
	{
        private $view;

		//move all this session interaction to HTTP Foundation (symfony)
		public function __construct($view)
		{
            $this->view = $view;

			if(!isset($_SESSION['msg']))
				$_SESSION['msg'] = [];
		}

		private function init($name)
		{
			if(!isset($_SESSION['msg'][$name]))
			{
				$_SESSION['msg'][$name]['err'] = [];
				$_SESSION['msg'][$name]['msg'] = [];
			}
		}

		public function addError($name,$str)
		{
			$this->init($name);

			$_SESSION['msg'][$name]['err'][] = $str;
		}

		public function addMessage($name,$str)
		{
			$this->init($name);
			
			$_SESSION['msg'][$name]['msg'][] = $str;
		}

		public function hasErrors()
		{
			foreach($_SESSION['msg'] as $v)
			{
				if(count($v['err']) > 0)
					return true;
			}
			return false;
		}

		public function getMessages($clear=true)
		{
            $messages = $_SESSION['msg'];

            $view = new View($this->view,compact(['messages']));
            $html = $view->render();

			if($clear)
			{
				$_SESSION['msg'] = [];
			}

			return $html;
		}
	}
?>
