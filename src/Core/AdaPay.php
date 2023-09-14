<?php

declare(strict_types=1);

namespace Deloz\Adapay\Core;

use Deloz\Adapay\Core\utils\AdaRequests;
use Exception;

/**
 * @Entity
 */
class AdaPay
{
    final public const SDK_VERSION = 'v1.4.4';
    public AdaRequests|string $ada_request = '';
    public AdaTools|string $ada_tools = '';
    public static string $api_key = '';
    public string $gateWayType = 'api';
    public string $gateWayUrl = '';
    public static array $header = ['Content-Type:application/json'];
    public static array $headerEmpty = ['Content-Type:multipart/form-data'];
    public static array $headerText = ['Content-Type:text/html'];
    protected static bool $isDebug;
    protected static string $logDir = '';
    protected static string $mqttAccessKey = 'LTAIOP5RkeiuXieW';
    protected static string $mqttAddress = 'post-cn-0pp18zowf0m.mqtt.aliyuncs.com:1883';

    // 不允许修改
    protected static string $mqttGroupId = 'GID_CRHS_ASYN';
    protected static string $mqttInstanceId = 'post-cn-0pp18zowf0m';
    protected string $postCharset = 'utf-8';
    protected array $result = [];
    protected static string $rsaPrivateKey = '';
    protected static string $rsaPrivateKeyFilePath = '';
    protected static string $rsaPublicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCwN6xgd6Ad8v2hIIsQVnbt8a3JituR8o4Tc3B5WlcFR55bz4OMqrG/356Ur3cPbc2Fe8ArNd/0gZbC9q56Eb16JTkVNA/fye4SXznWxdyBPR7+guuJZHc/VW2fKH2lfZ2P3Tt0QkKZZoawYOGSMdIvO+WqK44updyax0ikK6JlNQIDAQAB';
    protected string $signType = 'RSA2';
    protected int $statusCode = 200;

    public function __construct()
    {
        $this->ada_request = new AdaRequests();
        $this->ada_tools = new AdaTools();
        $this->getGateWayUrl($this->gateWayType);
        $this->__init_params();
    }

    private function __init_params(): void
    {
        $this->ada_tools->rsaPrivateKey = self::$rsaPrivateKey;
        $this->ada_tools->rsaPublicKey = self::$rsaPublicKey;
    }

    public function getGateWayUrl($type): void
    {
        $this->gateWayUrl = \defined('GATE_WAY_URL') ? \sprintf(GATE_WAY_URL, $type) : 'https://api.adapay.tech';
    }

    public static function init($config_info, $prod_mode = 'live', $is_object = false): void
    {
        if (empty($config_info)) {
            try {
                throw new Exception('缺少SDK配置信息');
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        if ($is_object) {
            $config_obj = $config_info;
        } else {
            if (!\file_exists($config_info)) {
                try {
                    throw new Exception('SDK配置文件不存在');
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
            $cfg_file_str = \file_get_contents($config_info);
            $config_obj = \json_decode($cfg_file_str, true);
        }

        $sdk_version = \defined('SDK_VERSION') ? self::SDK_VERSION : 'v1.0.0';
        self::$header['sdk_version'] = $sdk_version;
        self::$headerText['sdk_version'] = $sdk_version;
        self::$headerEmpty['sdk_version'] = $sdk_version;
        self::$isDebug = \defined('DEBUG') ? DEBUG : false;
        self::$logDir = \defined('DEBUG') ? LOG : __DIR__ . '/log';
        $project_env = \defined('ENV') ? ENV : 'prod';
        self::init_mqtt($project_env);

        if ('live' === $prod_mode) {
            self::$api_key = $config_obj['api_key_live'] ?? '';
        }

        if ('test' === $prod_mode) {
            self::$api_key = $config_obj['api_key_test'] ?? '';
        }

        if (isset($config_obj['rsa_public_key']) && $config_obj['rsa_public_key']) {
            self::$rsaPublicKey = $config_obj['rsa_public_key'];
        }

        if (isset($config_obj['rsa_private_key']) && $config_obj['rsa_private_key']) {
            self::$rsaPrivateKey = $config_obj['rsa_private_key'];
        }
    }

    public static function init_mqtt($project_env): void
    {
        if (isset($project_env) && 'test' === $project_env) {
            self::$mqttAddress = 'post-cn-459180sgc02.mqtt.aliyuncs.com:1883';
            self::$mqttGroupId = 'GID_CRHS_ASYN';
            self::$mqttInstanceId = 'post-cn-459180sgc02';
            self::$mqttAccessKey = 'LTAILQZEm73RcxhY';
        }
    }

    public function isError()
    {
        if (empty($this->result)) {
            return true;
        }
        $this->statusCode = $this->result[0];
        $resp_str = $this->result[1];
        $resp_arr = \json_decode($resp_str, true);
        $resp_data = $resp_arr['data'] ?? '';
        $resp_sign = $resp_arr['signature'] ?? '';
        $resp_data_decode = \json_decode($resp_data, true);

        if ($resp_sign && 401 !== $this->statusCode) {
            if ($this->ada_tools->verifySign($resp_sign, $resp_data)) {
                if (200 !== $this->statusCode) {
                    $this->result = $resp_data_decode;

                    return true;
                }
                $this->result = $resp_data_decode;

                return false;
            }
            $this->result = [
                'failure_code' => 'resp_sign_verify_failed',
                'failure_msg' => '接口结果返回签名验证失败',
                'status' => 'failed',
            ];

            return true;
        }
        $this->result = $resp_arr;

        return true;
    }

    public static function setApiKey($api_key): void
    {
        self::$api_key = $api_key;
    }

    public static function setRsaPublicKey($pub_key): void
    {
        self::$rsaPublicKey = $pub_key;
    }

    public static function writeLog($message, $level = 'INFO'): void
    {
        if (self::$isDebug) {
            if (!\is_dir(self::$logDir)) {
                \mkdir(self::$logDir, 0o777, true);
            }

            $log_file = self::$logDir . '/adapay_' . \date('Ymd') . '.log';
            $server_addr = '127.0.0.1';

            if (isset($_SERVER['REMOTE_ADDR'])) {
                $server_addr = $_SERVER['REMOTE_ADDR'];
            }
            $message_format = '[' . $level . '] [' . \gmdate('Y-m-d\\TH:i:s\\Z') . '] ' . $server_addr . ' ' . $message . "\n";
            $fp = \fopen($log_file, 'a+b');
            \fwrite($fp, $message_format);
            \fclose($fp);
        }
    }

    protected function do_empty_data(array $req_params): array
    {
        return \array_filter($req_params, static function ($v) {
            if (!empty($v) || '0' === $v) {
                return true;
            }

            return false;
        });
    }

    protected function get_request_header($req_url, $post_data, $header = [])
    {
        $header[] = 'Authorization:' . self::$api_key;
        $header[] = 'Signature:' . $this->ada_tools->generateSignature($req_url, $post_data);

        return $header;
    }

    private function handleResult()
    {
        $json_result_data = \json_decode($this->result[1], true);

        if (isset($json_result_data['data'])) {
            return \json_decode($json_result_data['data'], true);
        }

        return [];
    }
}
