<?php

namespace tests\php\embedded;

class encryptionTest extends \core\Controller
{
    public function decryptAsymmetric(){
        $data = $_POST['data'];
        var_dump($this->security_helper->asymmetricDecryption($data, $_SESSION['private_key']));
    }

    public function decryptSymmetric(){
        $data = $_POST['data'];
        $secret_key = hex2bin($_POST['secret_key']);
        var_dump($this->security_helper->symmetricDecryption($data, $secret_key));
    }

    public function encryptSymmetric(){
        $secret_key = hex2bin($_POST['secret_key']);
        $data = "hello world i love python";
        $encrypted_text = $this->security_helper->symmetricEncryption($data, $secret_key);
        $this->response->returnJson([
            'encrypted_text' => $encrypted_text,
            'decrypted_text' => $data,
            'secret_key' => bin2hex($secret_key)
        ], 200);
    }
}