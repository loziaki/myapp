<?php
namespace Service;

class Request
{
    public static function get($options)
    {
        $options['method'] = 'GET';
        return static::send($options);
    }

    public static function jsonPost($options)
    {
        if (isset($options['data'])) {
            $options['data'] = json_encode($options['data']);
        }

        $options = array_merge_recursive($options, array(
            'method' => 'POST',
            'headers' => array('Content-Type: application/json; charset=utf-8'),
        ));

        return static::send($options);
    }

    public static function formPost($options)
    {
        if (isset($options['data']) && is_array($option['data'])) {
            $options['data'] = $options['data'];
        }

        $options = array_merge_recursive($options, array(
            'method' => 'POST',
            'headers' => array('Content-Type: charset=utf-8'),
        ));

        return static::send($options);
    }

    public static function send($options, $raw2json = true)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $options['method']);
        curl_setopt($ch, CURLOPT_URL, $options['url']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (isset($options['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
        }

        if (isset($options['timeout'])) {
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $options['timeout']);
        }

        if (isset($options['data'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['data']);
        }

        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        if ($raw2json === true) {
            $body = json_decode($result, true);
        } else {
            $body = $result;
        }

        curl_close($ch);
        return compact('status', 'body');
    }
}
