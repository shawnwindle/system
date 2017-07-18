<?php
	namespace System\HTML;
	
	use System\HTTP\ServerEnv;

	use Illuminate\Database\Eloquent\Model as Eloquent;

	class Form
	{
		private static $model;

		public static function open($options)
		{
			$url = '';
			if(isset($options['url']))
				$url = $options['url'];
			else
				$url = ServerEnv::getPage();

			$style = '';
			if(isset($options['style']))
				$style = $options['style'];

			$method = 'GET';
			if(isset($options['method']))
				$method = strtoupper($options['method']);

			$method_alias = '';
			if($method != 'GET' && $method != 'POST')
			{
				$method_alias = static::hidden('_method',$method);
				$method = 'POST';
			}
		
			$attributes = 'action="'.$url.'" method="'.$method.'"';	
			if($style)
				$attributes .= ' style="'.$style.'"';

			return '<form '.$attributes.'>'.$method_alias;
		}

		public static function model(Eloquent $model,$options)
		{
			static::$model = $model;
			return static::open($options);
		}

		public static function close()
		{
			return '</form>';
		}
		
		public static function entities($value)
		{
			return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
		}

		private static function value($name,$value)
		{
			$val = $value;
			if(is_null($val))
			{
				if(isset($_POST[$name]))
					$val = $_POST[$name];
				else if(static::$model && static::$model->$name)
					$val = static::$model->$name;
			}
			return $val;
		}

		private static function attributes($attributes)
		{
			$atts = '';
			foreach($attributes as $k => $v)
			{
				$atts .= ' '.static::entities($k).'="'.static::entities($v).'"';
			}
			return $atts;
		}

		public static function password($name,$value,$attributes=[])
		{
			return static::input('password',$name,$value,$attributes);
		}

		public static function hidden($name,$value,$attributes=[])
		{
			return static::input('hidden',$name,$value,$attributes);
		}
		
		public static function text($name,$value,$attributes=[])
		{
			return static::input('text',$name,$value,$attributes);
		}

		private static function input($type,$name,$value,$attributes=[])
		{
			$value = static::value($name,$value);
			$html = '<input type="'.$type.'" ';

			if($name)
				$html .= 'name="'.static::entities($name).'" ';

			$html .= 'value="'.static::entities($value).'"';

			$html .= static::attributes($attributes);

			$html .= ' />';

			return $html;
		}

		public static function select($name,$options,$value,$attributes=[])
		{
			$value = static::value($name,$value);
			$html = '<select name="'.static::entities($name).'" '.static::attributes($attributes).'>';

			foreach($options as $k => $v)
			{
				$selected = '';
				if($k == $value)
					$selected = ' selected="selected"';
				$html .= '<option value="'.static::entities($k).'"'.$selected.'>'.static::entities($v).'</option>';
			}

			$html .= '</select>';
			return $html;
		}

		public static function textarea($name,$value,$attributes=[])
		{
			$value = static::value($name,$value);
			return '<textarea name="'.static::entities($name).'" '.static::attributes($attributes).'>'.$value.'</textarea>';
		}
		
		public static function checkbox($name,$option,$value,$attributes=[])
		{
			$value = static::value($name,$value);
			if($value == $option)
				$attributes['checked'] = 'checked';

			return static::input('checkbox',$name,$option,$attributes);
		}

		public static function submit($value=null,$attributes=[])
		{
			return static::input('submit',null,$value,$attributes);
		}
		
		public static function button($value=null,$attributes=[])
		{
			$html = '<button ';

			$html .= static::attributes($attributes);

			$html .= ' >'.static::entities($value).'</button>';

			return $html;
		}
	}
?>
