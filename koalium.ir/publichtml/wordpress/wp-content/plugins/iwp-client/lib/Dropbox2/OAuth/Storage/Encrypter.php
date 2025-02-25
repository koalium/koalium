<?php

/**
 * This class provides the functionality to encrypt
 * and decrypt access tokens stored by the application
 * @author Ben Tadiar <ben@handcraftedbyben.co.uk>
 * @link https://github.com/benthedesigner/dropbox
 * @package Dropbox\Oauth
 * @subpackage Storage
 */

class Dropbox_Encrypter
{    
    // Encryption settings - default settings yield encryption to AES (256-bit) standard
    // @todo Provide PHPDOC for each class constant
    const KEY_SIZE = 32;
    const IV_SIZE = 16;
    
    /**
     * Encryption key
     * @var null|string
     */
    private $key = null;
    
    /**
     * Check Mcrypt is loaded and set the encryption key
     * @param string $key
     * @return void
     */
    public function __construct($key)
    {
        if (preg_match('/^[A-Za-z0-9]+$/', $key) && $length = strlen($key) === self::KEY_SIZE) {
            # Short-cut so that the mbstring extension is not required
            $this->key = $key;
        } elseif (($length = mb_strlen($key, '8bit')) !== self::KEY_SIZE) {
            throw new Dropbox_Exception('Expecting a ' .  self::KEY_SIZE . ' byte key, got ' . $length);
        } else {
            // Set the encryption key
            $this->key = $key;
        }
    }
    
    /**
     * Encrypt the OAuth token
     * @param \stdClass $token Serialized token object
     * @return string
     */
    public function encrypt($token)
    {

        // Encryption: we always use phpseclib for this
        global $iwp_backup_core;
        $ensure_phpseclib = $iwp_backup_core->ensure_phpseclib('Crypt_AES');
        
        if (is_wp_error($ensure_phpseclib)) {
            $iwp_backup_core->log("Failed to load phpseclib classes (".$ensure_phpseclib->get_error_code()."): ".$ensure_phpseclib->get_error_message());
            $iwp_backup_core->log("Failed to load phpseclib classes (".$ensure_phpseclib->get_error_code()."): ".$ensure_phpseclib->get_error_message(), 'error');
            return false;
        }
        
        $iwp_backup_core->ensure_phpseclib('Crypt_Rijndael');

        if (!function_exists('crypt_random_string')) require_once($GLOBALS['iwp_mmb_plugin_dir'].'/lib/phpseclib/phpseclib/phpseclib/Crypt/Random.php');
        
        $iv = crypt_random_string(self::IV_SIZE);
        
        // Defaults to CBC mode
        $rijndael = new Crypt_Rijndael();
        
        $rijndael->setKey($this->key);
        
        $rijndael->setIV($iv);

        $cipherText = $rijndael->encrypt($token);
        
        return base64_encode($iv . $cipherText);
    }
    
    /**
     * Decrypt the ciphertext
     * @param string $cipherText
     * @return object \stdClass Unserialized token
     */
    public function decrypt($cipherText)
    {
    
        // Decryption: prefer mcrypt, if available (since it can decrypt data encrypted by either mcrypt or phpseclib)

        $cipherText = base64_decode($cipherText);
        $iv = substr($cipherText, 0, self::IV_SIZE);
        $cipherText = substr($cipherText, self::IV_SIZE);
    
        if (function_exists('mcrypt_decrypt')) {
            // @codingStandardsIgnoreLine
            $token = @mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, $cipherText, MCRYPT_MODE_CBC, $iv);
        } else {
            global $iwp_backup_core;
            $iwp_backup_core->ensure_phpseclib('Crypt_Rijndael');

            $rijndael = new Crypt_Rijndael();
            $rijndael->setKey($this->key);
            $rijndael->setIV($iv);
            $token = $rijndael->decrypt($cipherText);
        }
        
        return $token;
    }
}
