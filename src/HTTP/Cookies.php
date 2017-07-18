<?php
	namespace System\HTTP;

	use Symfony\Component\HttpFoundation\Cookie;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;

	class Cookies
	{
		private $request;
		private $response;

		public function __construct()
		{
			$this->request = Request::createFromGlobals();
			$this->response = new Response();
			//print_r($this->request->cookies);
		}

		public function setValue($key,$value)
		{
			$this->response->headers->setCookie(new Cookie('lastChangeTime', time(), time() + (3600 * 48)));
			//$this->response->send();
			print_r($this->request->cookies);
		}

		public function getValue($key)
		{
		}
	}
?>
