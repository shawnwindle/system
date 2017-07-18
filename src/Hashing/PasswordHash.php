<?php
	namespace System\Hashing;

	use Hautelook\Phpass\PasswordHash as hasher;

	class PasswordHash implements PasswordHashInterface
	{
		private $hasher;

		public function __construct()
		{
			$this->hasher = new hasher(10,true);
		}

		public function hashPassword($plaintext)
		{
			return $this->hasher->HashPassword($plaintext);
		}
		
		public function checkPassword($plaintext,$hash)
		{
			return $this->hasher->CheckPassword($plaintext,$hash);
		}
	}
?>
