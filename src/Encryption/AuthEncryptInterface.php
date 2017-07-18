<?php
    namespace System\Encryption;

    interface AuthEncryptInterface
    {
        function encrypt($plaintext);
        function decrypt($cyphertext);
    }
?>
