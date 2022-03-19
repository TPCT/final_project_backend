<?php

namespace helpers;

class SecurityHelper
{
    public function generateKeyPairs(&$private_key_output, &$public_key_output){
        $config = [
            'digest_alg' => "sha512",
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA
        ];
        $resource = openssl_pkey_new($config);
        openssl_pkey_export($resource, $private_key_output);
        $public_key_output = openssl_pkey_get_details($resource)['key'];
    }

    public function asymmetricEncryption($message, $public_key){
        openssl_public_encrypt($message, $encrypted_data, $public_key, OPENSSL_PKCS1_OAEP_PADDING);
        return bin2hex($encrypted_data);
    }

    public function asymmetricDecryption($encrypted_data, $private_key){
        $encrypted_data = hex2bin($encrypted_data);
        openssl_private_decrypt($encrypted_data, $decrypted_data, $private_key, OPENSSL_PKCS1_OAEP_PADDING);
        return $decrypted_data;
    }

    public function symmetricEncryption($message, $private_key, $cipher_algo="aes-256-cbc"): string{
        $iv_key = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher_algo));
        $encrypted_data = openssl_encrypt($message, $cipher_algo, $private_key, OPENSSL_RAW_DATA, $iv_key);
        $iv_key = bin2hex($iv_key);
        $encrypted_data = bin2hex($encrypted_data);
        $ciphered_text = "{$iv_key}:{$encrypted_data}";
        return base64_encode($ciphered_text);
    }

    public function symmetricDecryption($ciphered_text, $private_key, $cipher_algo="aes-256-cbc"): string{
        $ciphered_text = base64_decode($ciphered_text);
        [$iv_key, $encrypted_text] = explode(":", $ciphered_text);
        [$iv_key, $encrypted_text] = [hex2bin($iv_key), hex2bin($encrypted_text)];
        return openssl_decrypt($encrypted_text, $cipher_algo, $private_key, OPENSSL_RAW_DATA, $iv_key);
    }

    public function decryptSessionData(): bool{
        if (Helper::isLogged()){
            if (isset($_POST['data'], $_SESSION['secret_key'])) {
                $decrypted_data = $this->symmetricDecryption($_POST['data'], $_SESSION['secret_key']);
                if ($decrypted_data && $decrypted_data = json_decode($decrypted_data, True)) {
                    $_POST = $decrypted_data;
                    return True;
                }
            }
        }else {
            if (isset($_POST['data'], $_SESSION['private_key'])) {
                $decrypted_data = $this->asymmetricDecryption($_POST['data'], $_SESSION['private_key']);
                if ($decrypted_data !== Null && $decrypted_data = json_decode($decrypted_data, True)) {
                    $_POST = $decrypted_data;
                    return True;
                }
            }
        }
        return False;
    }

    public function encryptSessionData(mixed $data): array{
        if (Helper::isLogged() && isset($_SESSION['secret_key'])){
            $data = json_encode($data);
            $encrypted_data = $this->symmetricEncryption($data, $_SESSION['secret_key']);
            return [
                'data' => $encrypted_data
            ];
        }

        return [
            'data' => Null
        ];
    }

}

if (php_sapi_name() === "cli"){
    $security_helper = new SecurityHelper();
    $text = "hello world i love python";
    # symmetric encryption
    $private_key = bin2hex(openssl_random_pseudo_bytes(16));
    $encrypted_text = $security_helper->symmetricEncryption($text, $private_key);
    $decrypted_text = $security_helper->symmetricDecryption($encrypted_text, $private_key);
    echo "the message text: {$text}\n";
    echo "the encrypted text: {$encrypted_text}\n";
    echo "the decrypted text: {$decrypted_text}\n";

    #asymmetric encryption
    $security_helper->generateKeyPairs($private_key, $public_key);
    echo "private key: {$private_key}\n";
    echo "public key: {$public_key}\n";
    $encrypted_text = $security_helper->asymmetricEncryption($text, $public_key);
    $decrypted_text = $security_helper->asymmetricDecryption($encrypted_text, $private_key);
    echo "the message text: {$text}\n";
    echo "the encrypted text: {$encrypted_text}\n";
    echo "the decrypted text: {$decrypted_text}\n";
}