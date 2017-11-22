<?php
	namespace System\Files;

	class MimeType
	{
		private $mime_type;

		public function __construct($mime_type)
		{
			$this->mime_type = $mime_type;
		}

        public function isImage()
        {
            return in_array($this->mime_type,['image/jpeg','image/jpg','image/png','image/gif']);
        }
	}
?>
