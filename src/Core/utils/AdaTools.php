<?php

declare(strict_types=1);

namespace Deloz\Adapay\Core;

use Exception;

final class AdaTools
{
    public string $rsaPrivateKey = '';
    public string $rsaPrivateKeyFilePath = '';
    public string $rsaPublicKey = '';
    public string $rsaPublicKeyFilePath = '';

    public function checkEmpty($value)
    {
        if (!isset($value)) {
            return true;
        }

        if (null === $value) {
            return true;
        }

        if (\trim($value) === '') {
            return true;
        }

        return false;
    }

    public function createLinkstring($params)
    {
        $arg = '';

        foreach ($params as $key => $val) {
            if ($val) {
                $arg .= $key . '=' . $val . '&';
            }
        }

        return \substr($arg, 0, -1);
    }

    public function generateSignature($url, $params): string
    {
        if (\is_array($params)) {
            $Parameters = [];

            foreach ($params as $k => $v) {
                $Parameters[$k] = $v;
            }
            $data = $url . \json_encode($Parameters);
        } else {
            $data = $url . $params;
        }

        return $this->SHA1withRSA($data);
    }

    public function get_array_value($data, $key)
    {
        if (isset($data[$key])) {
            return $data[$key];
        }

        return '';
    }

    public function SHA1withRSA($data): string
    {
        if ($this->checkEmpty($this->rsaPrivateKeyFilePath)) {
            $priKey = $this->rsaPrivateKey;
            $key = "-----BEGIN PRIVATE KEY-----\n" . \wordwrap($priKey, 64, "\n", true) . "\n-----END PRIVATE KEY-----";
        } else {
            $priKey = \file_get_contents($this->rsaPrivateKeyFilePath);
            $key = \openssl_get_privatekey($priKey);
        }

        try {
            \openssl_sign($data, $signature, $key);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return \base64_encode($signature);
    }

    public function verifySign($signature, $data): bool
    {
        if ($this->checkEmpty($this->rsaPublicKeyFilePath)) {
            $pubKey = $this->rsaPublicKey;
            $key = "-----BEGIN PUBLIC KEY-----\n" . \wordwrap($pubKey, 64, "\n", true) . "\n-----END PUBLIC KEY-----";
        } else {
            $pubKey = \file_get_contents($this->rsaPublicKeyFilePath);
            $key = \openssl_get_publickey($pubKey);
        }

        if (\openssl_verify($data, \base64_decode($signature, true), $key)) {
            return true;
        }

        return false;
    }
}
