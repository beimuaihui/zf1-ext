<?php
class Baogg_Encrypt
{

	public static $key= 'zf1-ext/library/Baogg/Encrypt.php';


	public static function encrypt($plaintext,$key = ''){
		if(!$key){
			$key = self::$key;
		}
		$key = hash("SHA256", $key, true);
	    # create a random IV to use with CBC encoding
	    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	    
	    # creates a cipher text compatible with AES (Rijndael block size = 128)
	    # to keep the text confidential 
	    # only suitable for encoded input that never ends with value 00h
	    # (because of default zero padding)
	    $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,$plaintext, MCRYPT_MODE_CBC, $iv);

	    # prepend the IV for it to be available for decryption
	    $ciphertext = $iv . $ciphertext;
	    
	    # encode the resulting cipher text so it can be represented by a string
	    $ciphertext_base64 = base64_encode($ciphertext);

	    //echo  $ciphertext_base64 . "\n";
	    return $ciphertext_base64;
	}

	    # === WARNING ===

	    # Resulting cipher text has no integrity or authenticity added
	    # and is not protected against padding oracle attacks.
	    
	    # --- DECRYPTION ---
	public static function decrypt($ciphertext_base64,$key = '') {
		if(!$key){
			$key = self::$key;
		}
		$key = hash("SHA256", $key, true);

	 	# create a random IV to use with CBC encoding
	    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	    //$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

	    $ciphertext_dec = base64_decode($ciphertext_base64);
	    
	    # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
	    $iv_dec = substr($ciphertext_dec, 0, $iv_size);
	    
	    # retrieves the cipher text (everything except the $iv_size in the front)
	    $ciphertext_dec = substr($ciphertext_dec, $iv_size);

	    # may remove 00h valued characters from end of plain text
	    $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,$ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
	    
	    //echo  $plaintext_dec . "\n";
	    return $plaintext_dec;
	}


	public static function password($password,$seed='')
	{
		//fill at least 10 seed.
		if(!$seed || strlen($seed)<10){
			for ($i = 1; $i <= 10; $i++)
			       $seed = substr('0123456789abcdef', rand(0,15), 1).$seed;
		}
	    $seed =  substr($seed,-10);
	    return crypt($seed.trim($password).$seed).$seed;
	}

	public static function checkPassword($password, $stored_value){
		if (strlen($stored_value) <=10)
	      return FALSE;
	    $stored_seed = substr($stored_value,-10);
	    //echo __FILE__.__LINE__.'<pre>';var_dump($password);var_dump($stored_seed);var_dump(substr($stored_value,0,-10));var_dump(crypt($stored_seed.trim($password).$stored_seed,substr($stored_value,0,-10)));exit;
	    if (crypt($stored_seed.trim($password).$stored_seed,substr($stored_value,0,-10)).$stored_seed == $stored_value)
	    	return TRUE;
	    else
	    	return FALSE;
	}

	public static function genFormSecret($fields='',$key=''){
		return md5(uniqid(rand(), true));
		//must post in 1 day
		$key=$key?$key:date('Y-m-d',strtotime("+1 week 2 days"));
		$md5=md5(md5($fields).$date);
		return $md5;
	}

	public static function checkFormSecret($fields,$secret,$key=''){
		//must post in 1 day
		$arr_key=$key?array($key):array(date('Y-m-d',strtotime("+1 week 2 days")),date('Y-m-d:H',strtotime("+1 week 3 days")));
		foreach($arr_key as $key){
			if($secret === self::genFormSecret($fields,$key)){
				return true;
			}
		}
		return false;
	}
}