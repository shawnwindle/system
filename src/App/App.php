<?php	
	namespace System\App;

	use Symfony\Component\Finder\Finder;

    use Symfony\Component\Routing\RequestContext;
    use Symfony\Component\Routing\Matcher\UrlMatcher;
    
    use Symfony\Component\HttpFoundation\Response;
    
    use System\Support\Str;

    use System\App\Routes;

	class App
	{
		private $config;
        private $controller;

        //Note: used for backwards compat/eventually will disappear
        private $old_app;

		public function __construct($old_app=null)
        {
            $this->old_app = $old_app;
		}

		private function loadApp()
        {
            $this->runGlobalMiddleware();
            
            $ioc = ioc();

            $request = $ioc->resolve('request');

            //if _method set, move it to request method
            //to support PUT/DELETE 
            $method = $request->post('_method');
            $request = $request->getRequest();
            if($method)
                $request->setMethod($method);

            $response = $ioc->resolve('response');

            include(ROOT_PATH.'routes.php');

            try
            {
                $context = new RequestContext();
                $context->fromRequest($request);
                $matcher = new UrlMatcher(Routes::getRoutes(), $context);

                $path = $request->getPathInfo();

                //remove optional slash on the end to unifify routes
                if(Str::substring($path,-1) == '/')
                    $path = Str::substring($path,0,-1);
                
                //remove extension
                if(Str::substring($path,-4) == '.php')
                    $path = Str::substring($path,0,-4);

                $attributes = $matcher->match($path);

                $controller_parts = explode('@',$attributes['_controller']);
                if(count($controller_parts) == 2)
                {
                    $controllers_name = 'app\Controllers\\'.$controller_parts[0];

                    $this->controller = new $controllers_name();

                    $this->runLocalMiddleware();

                    $params = array();
                    $method = new \ReflectionMethod($controllers_name,$controller_parts[1]);

                    $parameters = $method->getParameters();

                    foreach($parameters as $parameter)
                    {
                        $type = $parameter->getType();
                        if($type)
                        {
                            //see if exists in IOC
                            if($ioc->has($type->__toString()))
                                $params[] = $ioc->resolve($type->__toString());
                        }
                        else
                        {
                            if(isset($attributes[$parameter->getName()]))
                                $params[] = $attributes[$parameter->getName()];
                        }
                    }

                    if(count($params) == count($parameters))
                    {
                        $view = call_user_func_array(array($this->controller,$controller_parts[1]),$params);
                        
                        if(!(is_object($view) && (get_class($view) == 'System\App\MVC\View')))
                        {
                            $content = $view.'';
                        }
                        else
                        {
                            $content = $view->render();
                        }

                        $response->setContent($content);
                        $response->setStatusCode(Response::HTTP_OK);
                        $response->headers->set('Content-Type', 'text/html');
                        $response->send();
                    }
                    else
                    {
                        //TODO: show error (exception)
                        echo 'Error: missing controller parameters';
                    }
                    exit;
                }
                else
                {
                    //TODO: show error (exception)
                    echo 'Error: controller format error';
                    exit;
                }
            }
            catch(\Exception $e)
            {
                //print_r($e);
                //exit;
                $this->runLocalMiddleware();

                if($this->old_app)
                    $this->old_app->run();
            }
		}

		private function loadProviders()
        {
			if(isset($this->config['provider']) && isset($this->config['provider']['providers']))
			{
				foreach($this->config['provider']['providers'] as $class)
				{
					$provider = new $class();
					$provider->register();
				}
			}
		}

		private function loadConfig()
		{
			$this->config = array();

			$finder = new Finder();
			$finder->files()->in(ROOT_PATH.'config/');

			foreach($finder as $file)
			{
				$name = $this->getConfigName($file);
				$this->config[$name] = include($file->getRealPath());
			}
		}

		private function getConfigName($file)
		{
			$filename = $file->getRelativePathname();
			$extension = $file->getExtension();
			
			$name = Str::substring($filename,0,(Str::length($extension)+1)*-1);
			return Str::stringToLowercase($name);
		}

		private function runGlobalMiddleware()
		{
			if(isset($this->config['middleware']) && isset($this->config['middleware']['global']))
			{
				$middleware = $this->config['middleware']['global'];
				foreach($middleware as $class)
                {
					$inst = new $class();
					$inst->handle();
                }
			}
		}
		
		private function runLocalMiddleware()
		{
			if(isset($this->config['middleware']))
            {
                $middleware = array();

                if($this->controller)
                    $middleware = $this->controller->getMiddleware();
                else
                {
                    $middleware = null;
                    if($this->old_app)
                        $middleware = $this->old_app->getMiddleware();
                }

                if(is_string($middleware))
                {
                    $this->runMiddleware($middleware);
                }
                else if(is_array($middleware))
                {
                    foreach($middleware as $name)
                    {
                        $this->runMiddleware($name);
                    }
                }
			}
		}

		private function runMiddleware($name)
		{
			if(isset($this->config['middleware']['local'][$name]))
			{
				$class = $this->config['middleware']['local'][$name];

				$inst = new $class();
				$inst->handle();
			}
		}

        private function loadAliases()
        {
            if(isset($this->config['alias']))
            {
                foreach($this->config['alias'] as $alias => $class)
                {
                    class_alias($class,$alias,true);
                }
            }
        }

        public function config($key)
        {
            $keys = explode('.',$key);

            $config = $this->config;
            foreach($keys as $key)
            {
                if(isset($config[$key]))
                    $config = $config[$key];
                else
                    return '';  //if at any point something is requested that does not exist
            }

            return $config;
        }

        //TODO: only needed as we move old plugins to new controllers, once done remove this function
        public function isRouteNewController()
        {
            return !is_null($this->controller);
        }

        public function load()
        {
			$this->loadConfig();
            $this->loadProviders();

            $ioc = ioc();

            //setup database connection
            $capsule = $ioc->resolve('database_manager');

            $queries_log = $this->config('system.database_queries_log');
            if($queries_log)
            {
                $connection = $capsule->getConnection();
                $connection->enableQueryLog();
            }
            
            $this->loadAliases();
        }

		public function run()
        {
            if(is_null($this->config))
                $this->load();

            $this->loadApp();
		}
	}
?>
