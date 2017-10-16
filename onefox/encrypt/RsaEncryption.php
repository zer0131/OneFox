<?php

/**
 * @author ryan<zer0131@vip.qq.com>
 * @desc
 * Rsa加密简单封装，基于openssl
 * 公钥和私钥成对出现，网上有生成方法，后面会封装进类
 *
 * Padding:
 * OPENSSL_PKCS1_PADDING
 * OPENSSL_SSLV23_PADDING
 * OPENSSL_PKCS1_OAEP_PADDING
 * OPENSSL_NO_PADDING
 *
 */

namespace onefox\encrypt;

class RsaEncryption {
    private $publicKeyRes;//公钥
    private $privateKeyRes;//私钥
    private $padding;

    public function __construct($pubKey, $privKey, $padding = OPENSSL_PKCS1_PADDING) {
        if (!extension_loaded('openssl')) {
            throw new \RuntimeException('The openssl extension must be loaded');
        }
        $this->publicKeyRes = openssl_pkey_get_public($pubKey);
        $this->privateKeyRes = openssl_pkey_get_private($privKey);
        $this->padding = $padding;
    }

    //公钥加密
    public function encryptWithPubKey($text) {
        if (openssl_public_encrypt($text, $encryptData, $this->publicKeyRes, $this->padding)) {
            return base64_encode($encryptData);
        }
        return false;
    }

    //私钥加密
    public function encryptWithPrivKey($text) {
        if (openssl_private_encrypt($text, $encryptData, $this->privateKeyRes, $this->padding)) {
            return base64_encode($encryptData);
        }
        return false;
    }

    //公钥解密
    public function decryptWithPubKey($text) {
        if (openssl_public_decrypt(base64_decode($text), $decryptData, $this->publicKeyRes, $this->padding)) {
            return $decryptData;
        }
        return false;
    }

    //私钥解密
    public function decryptWithPrivKey($text) {
        if (openssl_private_decrypt(base64_decode($text), $decryptData, $this->privateKeyRes, $this->padding)) {
            return $decryptData;
        }
        return false;
    }
}
