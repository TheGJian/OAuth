<?php
/**
 * Created by PhpStorm.
 * User: zguangjian
 * Date: 2020/3/25
 * Time: 10:44
 * Email: zguangjian@outlook.com
 */

namespace zguangjian;


class Http
{
    /**
     * @param $url
     * @param array $params
     * @param string $method
     * @param array $header
     * @param bool $multi
     * @return bool|string
     * @throws \Exception
     */
    public static function request($url, $params = [], $method = 'GET', $header = [], $multi = false)
    {
        $opts = array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => $header
        );

        /* 根据请求类型设置特定参数 */
        switch (strtoupper($method)) {
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new \Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) throw new \Exception('请求发生错误：' . $error);
        return $data;
    }

    public static function urlSplit($url, $param = [])
    {
        return $url . '?' . http_build_query($param);
    }

    /**
     * @param string $string
     * @return mixed
     */
    public static function requestJson(string $string, $assoc = true)
    {
        $content = json_decode($string, $assoc);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(sprintf(
                "Failed to parse JSON response: %s",
                json_last_error_msg()
            ));
        }

        return $content;
    }
}