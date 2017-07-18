<?php
	namespace System\Hashing;

	interface PasswordHashInterface
	{
		function hashPassword($plaintext);
		function checkPassword($plaintext,$hash);
	}
?>
