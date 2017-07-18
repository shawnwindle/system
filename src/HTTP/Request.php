<?php
    namespace System\HTTP;

    use Symfony\Component\HttpFoundation\Request;

    use System\HTTP\ServerEnv;

    use System\Support\Str;

    class Request
    {
        private $request;

        public function __construct()
        {
            $this->request = Request::createFromGlobals();
        }

        public function getRequest()
        {
            return $this->request;
        }

        public function get($key)
        {
            return $this->request->query->get($key);
        }

        public function hasGet($key)
        {
            return $this->request->query->has($key);
        }

        public function allGet()
        {
            return $this->request->query->all();
        }

        public function post($key)
        {
            return $this->request->request->get($key);
        }

        public function hasPost($key)
        {
            return $this->request->request->has($key);
        }

        public function allPost()
        {
            return $this->request->request->all();
        }

        public function file($key)
        {
            return $this->request->files->get($key);
        }

        public function allFile()
        {
            return $this->request->files->all();
        }

        public function isWhitelist()
        {
            $ip = ServerEnv::getIPAddress();

            //always whitelist local ip addresses
            if(Str::substring($ip,0,8) == '192.168.')
                return true;

            $app = app();
            $whitelist = $app->config('http.whitelist_ips');

            return in_array($ip, $whitelist);
        }
    }
?>
