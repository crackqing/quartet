<?php
namespace  App\Service\Common;

/** EasyWechat 功能统一封装
 * Class Wechat
 * @package App\Service\Common
 */
class Wechat
{
    protected $config;
    public function __construct()
    {
        $this->config = [
            'app_id' => \Config::get('services.weixin.client_id'),
            'secret' => \Config::get('services.weixin.client_secret'),

            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array',
//            'scopes'   => ['snsapi_userinfo'],
//            'callback' => '/test',
            'log' => [
                'level' => 'debug',
                'file' => storage_path('/logs/wechat.log'),
            ],
        ];
    }
    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = !empty($config) && is_array($config) ? array_merge($this->config,$config) : $this->config;
    }
    /** https://mp.weixin.qq.com/debug/cgi-bin/sandboxinfo?action=showinfo&t=sandbox/index 返回easyWechat实例
     * @return mixed
     */
    public function easyWechat()
    {
        return  \EasyWeChat\Factory::officialAccount($this->config);
    }
}