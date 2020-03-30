<?php
namespace Middleware;

class GetWxInfo extends \Framework\Middleware
{
    const WX_HEADER_CODE = 'code';
    const WX_HEADER_ENCRYPTED_DATA = 'encryptedData';
    const WX_HEADER_IV = 'iv';

    public function handle(&$customParams, $request)
    {
        //copy from wafer sdk ：loginservice
        $code = $request->headers->get(self::WX_HEADER_CODE);
        $encryptedData = $request->headers->get(self::WX_HEADER_ENCRYPTED_DATA);
        $iv = $request->headers->get(self::WX_HEADER_IV);
        $all = $request->headers->all();

        $customParams['wx_code'] = $code;
        $customParams['wx_encryptedData'] = $encryptedData;
        $customParams['wx_iv'] = $iv;

        return true;
    }
}