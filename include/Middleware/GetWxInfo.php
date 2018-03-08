<?php
namespace Middleware;

use Framework\MyApp;
use \Framework\MiddlewareInterface;

class GetWxInfo implements MiddlewareInterface
{
    const WX_HEADER_CODE = 'code';
    const WX_HEADER_ENCRYPTED_DATA = 'encryptedData';
    const WX_HEADER_IV = 'iv';

    public function handle(): bool
    {
        //copy from wafer sdk ：loginservice
        $code = MyApp::$request->headers->get(self::WX_HEADER_CODE);
        $encryptedData = MyApp::$request->headers->get(self::WX_HEADER_ENCRYPTED_DATA);
        $iv = MyApp::$request->headers->get(self::WX_HEADER_IV);
        $all = MyApp::$request->headers->all();

        MyApp::$preset->set('wx_code',$code);
        MyApp::$preset->set('wx_encryptedData',$encryptedData);
        MyApp::$preset->set('wx_iv',$iv);

        return true;
    }
}