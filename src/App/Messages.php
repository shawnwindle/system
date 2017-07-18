<?php
	namespace System\App;

	class Messages
	{
		//move all this session interaction to HTTP Foundation (symfony)
		public function __construct()
		{
			if(!isset($_SESSION['msg']))
				$_SESSION['msg'] = array();
		}

		private function init($name)
		{
			if(!isset($_SESSION['msg'][$name]))
			{
				$_SESSION['msg'][$name]['err'] = array();
				$_SESSION['msg'][$name]['msg'] = array();
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

		//TODO: move layout out of this class (view)
		public function getMessages($clear=true)
		{
			$ret = '';

			foreach($_SESSION['msg'] as $k => $v)
			{
				if(count($v['err']) > 0 || count($v['msg']) > 0)
				{
					$ret .= '<table width="100%" cellpadding="0" cellspacing="0">';
					foreach($v['err'] as $v2)
					{
						$ret .= '<tr><td bgcolor="#CD0000"><div style="color: white;margin: 5px;">'.$v2.'</div></td></tr>';
					}
					
					foreach($v['msg'] as $v2)
					{
						$ret .= '<tr><td bgcolor="#094AB2"><div style="color: white;margin: 5px;">'.$v2.'</div></td></tr>';
					}
					$ret .= '</table>';
				}
			}

			if($clear)
			{
				$_SESSION['msg'] = array();
			}

			return $ret;
		}
	}
?>
