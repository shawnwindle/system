<?php
	namespace System\HTTP;

	use Symfony\Component\HttpFoundation\RedirectResponse;

	use System\HTTP\URL;

	class Redirect
	{
		private $redirect;

		private $url;

		public function __construct($url, $redirect = true)
		{
			if(is_string($url))
				$this->redirect = new RedirectResponse($url);
			else if(is_object($url) && get_class($url) == 'System\HTTP\URL')
				$this->redirect = new RedirectResponse($url->toString());
			else
				throw new \InvalidArgumentException('Argument #1 must be either a string or System\HTTP\URL.');

			if($redirect)
				$this->redirect();
		}

		public function redirect()
		{
			$this->redirect->send();
			exit;
		}
	}
?>
