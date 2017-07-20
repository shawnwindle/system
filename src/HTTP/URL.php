<?php
	namespace System\HTTP;

	use System\HTTP\ServerEnv;
	use System\HTTP\Schema;
    
    use System\Support\Str;

	class URL
	{
		private $schema;
		private $domain;
		private $path;
		private $query;

		public function __construct($path = null, $query = null,$domain = null, $schema = null)
		{
			if($schema)
				$this->setSchema($schema);

			if($domain)
				$this->setDomain($domain);

			if($path)
				$this->setPath($path);

			if($query)
				$this->setQuery($query);
		}

        public static function getURL($url_string)
        {
            $url_parts = parse_url($url_string);

            $url = new URL();
            if($url_parts !== false)
            {
                if(isset($url_parts['host']))
                    $url->setDomain($url_parts['host']);
                if(isset($url_parts['scheme']))
                    $url->setScheme($url_parts['scheme']);
                if(isset($url_parts['path']))
                    $url->setPath($url_parts['path']);

                if(isset($url_parts['query']))
                {
                    $query = [];
                    parse_str($url_parts['query'],$query);

                    $url->setQuery($query);
                }
            }
            else
                throw new \InvalidArgumentException('Argument #1 contains invalid url.');

            return $url;
        }

        public static function buildURLFromGlobals($ignore_query=[])
        {
            $query = [];
            $keys = array_keys($_GET);
            foreach($keys as $key)
            {
                if(!in_array($key,$ignore_query))
                {
                    $query[] = $key.'='.$_GET[$key];
                }
            }

            if(count($query))
                $query = implode('&',$query);
            else
                $query;

            return ServerEnv::getSchema().'://'.ServerEnv::getDomain().'/'.ServerEnv::getPage().($query ? '?'.$query : '');
        }

		public function setDomain($domain)
		{
			$this->domain = $domain;
		}

		public function getDomain()
		{
			return $this->domain;
		}
		
		public function setSchema($schema)
		{
			if(Schema::HTTP_SCHEMA == $schema || Schema::HTTPS_SCHEMA == $schema)
				$this->schema = $schema;
			else
				throw new \InvalidArgumentException('Argument #1 contains invalid schema.');
		}

		public function getSchema()
		{
			return $this->schema;
		}
		
		public function setPath($path)
        {
            if(Str::substring($path,0,1) == '/')
                $path = Str::substring($path,1);

            if(Str::substring($path,-1) == '/')
                $path = Str::substring($path,0,Str::length($path)-1);

			$this->path = $path;
		}

		public function getPath()
		{
			return $this->path;
		}
		
		public function setQuery($query)
		{
			if(is_array($query))
				$this->query = $query;
			else
				throw new \InvalidArgumentException('Argument #1 must be an array.');
		}

		public function getQuery()
		{
			return $this->query;
		}

		public function toString()
		{
			$url = '';

			if($this->schema)
				$url = $this->schema;
			else
			{
				$url = ServerEnv::getSchema();
			}

			$url .= '://';

			if($this->domain)
				$url .= $his->domain;
			else
				$url .= ServerEnv::getDomain();
			
			if(substr($url,-1) != '/')
				$url .= '/';

			if($this->path)
				$url .= $this->path;

			if($this->query && count($this->query))
			{
				$url .= '?';
				
				$query = array();
				foreach($this->query as $k => $v)
				{
					$query[] = $k.'='.urlencode($v);
				}
				$url .= implode('&',$query);
			}

			return $url;
		}

		public function __toString()
		{
			return $this->toString();
		}
	}
?>
