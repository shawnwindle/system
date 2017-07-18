<?php
	namespace System\Support;

	//String helper functions to wrap internal function so that UTF-8 is used
	//to support unicode
	class Str
    {
        public static function trim($string)
        {
            return trim($string);
        }

		public static function length($string)
		{
			return mb_strlen($string);
		}

		public static function randomString($length = 40)
		{
			//problem/metacharacters
			$invalidCharacters = array('+','/','=');

			$string = '';
			while(($stringLength = static::length($string)) < $length)
			{
				$bytes = static::randomBytes($length-$stringLength);

				//remove invalid base64 characters because this function
				//is used to make tokens (passed in POST or through GET in URL)
				$string .= static::stringReplace($invalidCharacters,'',base64_encode($bytes));
			}

			return static::substring($string,0,$length);
		}

		public static function substring($string, $start, $length = null)
		{
			return mb_substr($string, $start, $length, 'UTF-8');
		}

		public static function stringReplace($search, $replace, $subject, $count = null)
		{
			//str_replace is multi-byte character aware
			return str_replace($search, $replace, $subject, $count);
		}

		public static function randomBytes($length = 40)
		{
			return random_bytes($length);
		}

		public static function stringToLowercase($str)
		{
			return mb_strtolower($str,'UTF-8');
        }

        public static function stringPosition($haystack,$needle,$offset=0)
        {
            return mb_strpos($haystack,$needle,$offset,'UTF-8');
        }
	}
?>
