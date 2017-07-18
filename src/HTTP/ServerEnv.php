<?php
	namespace System\HTTP;

	use System\Support\Str;
	use System\HTTP\Schema;

	class ServerEnv
	{
		public static function getSchema()
		{
			return (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on') ? 
					Schema::HTTPS_SCHEMA : 
					Schema::HTTP_SCHEMA);
		}
        
        public static function getServerIPAddress()
        {
            if(isset($_SERVER['SERVER_ADDR']))
                return $_SERVER['SERVER_ADDR'];

            $ips = explode(' ',`hostname -I`);
            $ips = array_filter($ips,function($val) {
                return trim($val);
            });

            return end($ips);
        }

        public static function getIPAddress()
        {
            return $_SERVER['REMOTE_ADDR'];
        }

		public static function getDomain()
		{
			return $_SERVER['HTTP_HOST'];
		}

		public static function getPage()
        {
            $page = Str::substring($_SERVER['REQUEST_URI'],1);

            $pos = Str::stringPosition($page,'?');
            if($pos !== false)
            {
                $page = Str::substring($page,0,$pos);
            }

            return $page;
		}
	}
?>
