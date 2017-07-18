<?php
    namespace System\App\MVC;

    class Controller
    {
        protected $middleware;

        public function getMiddleware()
        {
            return $this->middleware;
        }
    }
?>
