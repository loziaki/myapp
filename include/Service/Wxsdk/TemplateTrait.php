<?php
namespace Service\Wxsdk;

use Service\ApiRequest;

trait TemplateTrait
{
    /**
     * 组合模板并添加至帐号下的个人模板库
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.send.html
     */
    public function addTemplate($accessToken, $templateId, $kidList, $sceneDesc = null)
    {
        $requestParams = [
            'tid' => $templateId,
            'kidList' => $kidList
        ];

        if ($sceneDesc != null) {
            $requestParams['sceneDesc'] = $sceneDesc;
        }

        list($status, $body) = array_values(ApiRequest::jsonPost([
            'url' => 'https://api.weixin.qq.com/wxaapi/newtmpl/addtemplate?access_token='.  $accessToken,
            'timeout' => 3000,
            'data' => $requestParams
        ]));

        if ($status !== 200 || !$body) {
            throw new WxsdkException('no reply receive from weixin');
        }

        if (isset($body['errcode']) && $body['errcode'] != 0) {
            throw new WxsdkException('['.$body['errcode'].']'.$body['errmsg']);
        }

        return $body;
    }

    /**
     * 获取当前帐号下的个人模板列表
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.getTemplateList.html
     */
    public function getTemplateList($accessToken)
    {
        $requestParams = [
            'access_token' => $accessToken
        ];

        list($status, $body) = array_values(ApiRequest::get([
            'url' => 'https://api.weixin.qq.com/wxaapi/newtmpl/gettemplate?'.  http_build_query($requestParams),
            'timeout' => 3000,
        ]));

        if ($status !== 200 || !$body) {
            throw new WxsdkException('no reply receive from weixin');
        }

        if (isset($body['errcode']) && $body['errcode'] != 0) {
            throw new WxsdkException('['.$body['errcode'].']'.$body['errmsg']);
        }

        return $body;
    }

    /**
     * 删除帐号下的个人模板
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.deleteTemplate.html
     */
    public function deleteTemplate($accessToken, $templateId)
    {
        $requestParams = [
            'tid' => $templateId,
        ];

        list($status, $body) = array_values(ApiRequest::jsonPost([
            'url' => 'https://api.weixin.qq.com/wxaapi/newtmpl/deltemplate?access_token='.  $accessToken,
            'timeout' => 3000,
            'data' => $requestParams
        ]));

        if ($status !== 200 || !$body) {
            throw new WxsdkException('no reply receive from weixin');
        }

        if (isset($body['errcode']) && $body['errcode'] != 0) {
            throw new WxsdkException('['.$body['errcode'].']'.$body['errmsg']);
        }

        return $body;
    }

    /**
     * 获取帐号所属类目下的公共模板标题
     * https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.getPubTemplateTitleList.html
     */
    public function getPubTemplateTitleList($accessToken, $ids, $start = 0, $limit = 30)
    {
        $requestParams = [
            'access_token' => $accessToken,
            'ids' => $ids,
            'start' => $start,
            'limit' => $limit
        ];

        list($status, $body) = array_values(ApiRequest::get([
            'url' => 'https://api.weixin.qq.com/wxaapi/newtmpl/getpubtemplatetitles?'. http_build_query($requestParams),
            'timeout' => 3000
        ]));

        if ($status !== 200 || !$body) {
            throw new WxsdkException('no reply receive from weixin');
        }

        if (isset($body['errcode']) && $body['errcode'] != 0) {
            throw new WxsdkException('['.$body['errcode'].']'.$body['errmsg']);
        }

        return $body;
    }

    public function sendMsg($accessToken, $options)
    {
        $standard = ['touser', 'template_id', 'page', 'data', 'miniprogram_state', 'lang'];
        $requestParams = array_intersect_key($options, array_flip($standard));

        list($status, $body) = array_values(ApiRequest::jsonPost([
            'url' => 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token='.  $accessToken,
            'timeout' => 3000,
            'data' => $requestParams
        ]));

        if ($status !== 200 || !$body) {
            throw new WxsdkException('no reply receive from weixin');
        }

        if (isset($body['errcode']) && $body['errcode'] != 0) {
            throw new WxsdkException('['.$body['errcode'].']'.$body['errmsg']);
        }

        return $body;
    }
}
