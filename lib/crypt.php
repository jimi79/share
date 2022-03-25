<?php
/**
 * Define the number of blocks that should be read from the source file for each chunk.
 * For 'AES-128-CBC' each block consist of 16 bytes.
 * So if we read 10,000 blocks we load 160kb into memory. You may adjust this value
 * to read/write shorter or longer chunks.
 */
define('FILE_ENCRYPTION_BLOCKS', 10000);

/**
 * Encrypt the passed file and saves the result in a new file with ".enc" as suffix.
 * 
 * param string $source Path to file that should be encrypted
 * param string $key	The key used for the encryption
 * param string $dest   File name where the encryped file should be written to.
 * return string|false  Returns the file name that has been created or FALSE if an error occured
 */

function encrypt_file($source, $key, $dest)
{
	$key = substr(sha1($key, true), 0, 16);
	$iv = openssl_random_pseudo_bytes(16);

	$error = false;
	if ($fpOut = fopen($dest, 'w')) {
		// Put the initialzation vector to the beginning of the file
		fwrite($fpOut, $iv);
		if ($fpIn = fopen($source, 'rb')) {
			while (!feof($fpIn)) {
				$plaintext = fread($fpIn, 16 * FILE_ENCRYPTION_BLOCKS);
				$ciphertext = openssl_encrypt($plaintext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
				// Use the first 16 bytes of the ciphertext as the next initialization vector
				$iv = substr($ciphertext, 0, 16);
				fwrite($fpOut, $ciphertext);
			}
			fclose($fpIn);
		} else {
			$error = true;
		}
		fclose($fpOut);
	} else {
		$error = true;
	}

	return $error ? false : $dest;
}

/**
 * Dencrypt the passed file and saves the result in a new file, removing the
 * last 4 characters from file name.
 * 
 * param string $source Path to file that should be decrypted
 * param string $key	The key used for the decryption (must be the same as for encryption)
 * param string $dest   File name where the decryped file should be written to.
 * return string|false  Returns the file name that has been created or FALSE if an error occured
 */
function decrypt_file($source, $key, $dest)
{
	$key = substr(sha1($key, true), 0, 16);

	$error = false;
	$ok = true;
	if ($dest != "") {
		$ok = ($fpOut = fopen($dest, 'w'));
	}
	if ($ok) {
		if ($fpIn = fopen($source, 'rb')) {
			// Get the initialzation vector from the beginning of the file
			$iv = fread($fpIn, 16);
			while (!feof($fpIn)) {
				$ciphertext = fread($fpIn, 16 * (FILE_ENCRYPTION_BLOCKS + 1)); // we have to read one block more for decrypting than for encrypting
				$plaintext = openssl_decrypt($ciphertext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
				// Use the first 16 bytes of the ciphertext as the next initialization vector
				$iv = substr($ciphertext, 0, 16);
								if ($dest == "") {
									print($plaintext); 
								} else {
									fwrite($fpOut, $plaintext);
								}
			}
			fclose($fpIn);
		}
		if ($dest != "") {
			$ok = fclose($fpOut);
		}
	}
	return $ok;
}
