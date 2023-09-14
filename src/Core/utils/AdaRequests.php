<?php

declare(strict_types=1);

namespace Deloz\Adapay\Core\utils;

use Deloz\Adapay\Core\AdaPay;
use const CURLINFO_HTTP_CODE;
use const CURLOPT_FAILONERROR;
use const CURLOPT_HEADER;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_POST;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_SSL_VERIFYPEER;
use const CURLOPT_URL;
use const JSON_UNESCAPED_UNICODE;

final class AdaRequests
{
    public string $postCharset = 'utf-8';

    public function characet($data, $targetCharset)
    {
        if (!empty($data)) {
            $fileType = $this->postCharset;

            if (\strcasecmp($fileType, $targetCharset) !== 0) {
                $data = \mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }

        return $data;
    }

    public function curl_request($url, $postFields = null, $headers = null, $is_json = false): array
    {
        AdaPay::writeLog('curl方法参数:' . \json_encode(\func_get_args(), JSON_UNESCAPED_UNICODE), 'INFO');
        $ch = \curl_init();
        \curl_setopt($ch, CURLOPT_URL, $url);
        \curl_setopt($ch, CURLOPT_FAILONERROR, false);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, CURLOPT_HEADER, 0);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if (\is_array($postFields) && 0 < \count($postFields)) {
            \curl_setopt($ch, CURLOPT_POST, true);

            if ($is_json) {
                $json_data = \json_encode($postFields);
                AdaPay::writeLog('post-json请求参数:' . \json_encode($postFields, JSON_UNESCAPED_UNICODE), 'INFO');
                $headers[] = 'Content-Length:' . \strlen($json_data);
                \curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
            } else {
                AdaPay::writeLog('post-form请求参数:' . \json_encode($postFields, JSON_UNESCAPED_UNICODE), 'INFO');
                \curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            }
        }

        if (empty($headers)) {
            $headers = ['Content-type: application/x-www-form-urlencoded'];
        }
        AdaPay::writeLog('curl请求头:' . \json_encode($headers, JSON_UNESCAPED_UNICODE), 'INFO');
        \curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = \curl_exec($ch);
        $statuCode = \curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (\curl_errno($ch)) {
            AdaPay::writeLog(\curl_error($ch), 'ERROR');
        }
        \curl_close($ch);
        AdaPay::writeLog('curl返回参数:' . $statuCode . \json_encode($response, JSON_UNESCAPED_UNICODE), 'INFO');

        return [$statuCode, $response];
    }
}
