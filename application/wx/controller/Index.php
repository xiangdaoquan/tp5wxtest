<?php

namespace app\wx\controller;

use app\wx\model\WxConfig;
use think\Request;

class Index
{
    /**
     * @var \think\Request Request实例
     */
    protected $request;

    /**
     * 构造方法
     * @param Request $request Request对象
     * @access public
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {

        $appID = 'wx3424c7920aa7b76a';
        $appsecret = '3d9be1ed74484ee7706e7158f8af0dca';
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appID}&secret={$appsecret}";
        dump(file_get_contents($url));
        //return $this->request->param('echostr');
    }

    public function getAccessToken()
    {
        $wxInfo = WxConfig::where('is_open', 1)->all();
        foreach ($wxInfo as $v) {
            $appID = $v['appid'];
            $appsecret = $v['appsecret'];
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appID}&secret={$appsecret}";
            $result = file_get_contents($url);
            $result = json_decode($result);
            if (isset($result['access_token']) && isset($result['expires_in'])) {
                WxConfig::where('id', $v['id'])
                    ->update(
                        ['access_token' => $result['access_token'],
                            'expires_in' => $result['expires_in']
                        ]);
            } else {
                WxConfig::where('id', $v['id'])
                    ->update(
                        ['err_code' => $result['access_token'],
                            'err_msg' => $result['expires_in']
                        ]);
            }
        }

    }
}
