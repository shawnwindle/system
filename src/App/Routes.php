<?php
    namespace System\App;

    use Symfony\Component\Routing\RouteCollection;
    use Symfony\Component\Routing\Route;

    use System\Support\Str;

    class Routes
    {
        private static $routes;

        private static function init()
        {
            if(!self::$routes)
                self::$routes = new RouteCollection();
        }

        public static function getRoutes()
        {
            return self::$routes;
        }

        public static function addRoute($method,$url,$controller,$routeName)
        {
            self::init();

            //add controller that request will be dispatched to
            $options = array('_controller' => $controller);

            //to allow optional placeholders, match placeholders followed by '?'
            $matches = array();
            preg_match_all('/\{([a-zA-Z0-9_-]*)\?\}/',$url,$matches);

            //for each placeholder that is followed by '?', 
            //add an empty default to make it optional
            foreach($matches[1] as $match)
            {
                if($match)
                    $options[$match] = '';
            }

            //remove all '?' so url can be matched
            $url = Str::stringReplace('?}','}',$url);

            $methods = array($method);
            //HEAD is same as GET, but without response body
            if($method == 'GET')
                $methods[] = 'HEAD';

            self::$routes->add($routeName, new Route($url, $options,
                array(), array(), '', array(), $methods));
        }

        public static function getRoute($routeName)
        {
            $route = null;
            if(self::$routes)
                $route = self::$routes->get($routeName);

            if($route)
                return $route;
            throw new \InvalidArgumentException('Route does not exist');
        }

        public static function get($url,$controller,$routeName)
        {
            self::addRoute('GET',$url,$controller,$routeName);
        }

        public static function post($url,$controller,$routeName)
        {
            self::addRoute('POST',$url,$controller,$routeName);
        }

        public static function delete($url,$controller,$routeName)
        {
            self::addRoute('DELETE',$url,$controller,$routeName);
        }

        public static function put($url,$controller,$routeName)
        {
            self::addRoute('PUT',$url,$controller,$routeName);
        }
    }
?>
