<?php
    namespace System\Encryption;

    class AuthEncrypt implements AuthEncryptInterface
    {
        private $key;

        public function __construct($key)
        {
            $this->key = $key;
        }

        public function encrypt($plaintext)
        {
            $hmac = $this->hmac_sign($plaintext);

            $plaintext = $hmac.$plaintext;

            $iv = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);

            return base64_encode($iv.mcrypt_encrypt(MCRYPT_RIJNDAEL_128,$this->key,$plaintext,MCRYPT_MODE_CBC,$iv));
        }

        public function decrypt($cyphertext)
        {
            $decoded = base64_decode($cyphertext);
            $iv = mb_substr($decoded, 0, 16, '8bit');
            $ciphertext = mb_substr($decoded, 16, null, '8bit');

            $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$this->key,$ciphertext,MCRYPT_MODE_CBC,$iv),"\0");

            $message = '';
            if($this->hmac_verify($decrypted))
            {
                $message = $this->hmac_message($decrypted);
            }
            else
            {
                throw new \Exception('HMAC verify failed');
            }

            return $message;
        }

        private function hmac_sign($message)
        {
            return hash_hmac('sha256', $message, $this->key);
        }

        private function hmac_verify($bundle)
        {
            $hmac = $this->hmac_hmac($bundle);
            $message = $this->hmac_message($bundle);

            return hash_equals($this->hmac_sign($message),$hmac);
        }

        private function hmac_hmac($bundle)
        {
            return mb_substr($bundle, 0, 64, '8bit');
        }

        private function hmac_message($bundle)
        {
            return mb_substr($bundle, 64,null, '8bit');
        }
    }
?>
