<?php

namespace Crypto;

final class CryptoUtils
{
    public static function encrypt($data, $pemCertPath)
    {
        if (!file_exists($pemCertPath)) {
            throw new \Exception('PEM cert file not found.');
        }

        $keyContent = file_get_contents($pemCertPath);

        $crypted = null;
        if (false === openssl_public_encrypt($data, $crypted, $keyContent)) {
            throw new \Exception('Error encrypting using cert.');
        }

        return $crypted;
    }

    public static function sign($data, $pemKeyPath, $algo = OPENSSL_ALGO_SHA1)
    {
        if (!file_exists($pemKeyPath)) {
            throw new \Exception('PEM key file not found.');
        }

        $keyContent = file_get_contents($pemKeyPath);

        $sign = null;
        if (false === openssl_sign($data, $sign, $keyContent, $algo)) {
            throw new \Exception('Error signing.');
        }

        return $sign;
    }

    public static function verifySign($data, $sign, $pemCertPath, $algo = OPENSSL_ALGO_SHA1)
    {
        if (!file_exists($pemCertPath)) {
            throw new \Exception('PEM cert file not found.');
        }

        $certContent = file_get_contents($pemCertPath);

        return 1 === openssl_verify($data, $sign, $certContent);
    }
}
