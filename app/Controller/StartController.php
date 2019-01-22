<?php

namespace App\Controller;

use Amber\System\Libraries\Input;

class StartController extends AbstractController
{
    /**
     *开始广告接口，注意这里的换行：应该是LF的，但我本地是wamp,用了CRLF
     * @desc 文档地址：http://amber-frame.com/doc?api=start/banner
     * @apiparam {"name":"device_type", "type":"string", "desc":"设备类型","default":"ios", "require":true}
     * @apireturn {"name":"code", "type":"int", "desc":"200正常", "require":true}
     * @apireturn {"name":"data", "type":"array", "desc":"开始广告详情", "require":true}
     * @apireturn {"name":"desc", "type":"string", "desc":"广告描述", "require":true}
     * @apireturn {"name":"title", "type":"string", "desc":"广告标题", "require":true}
     * @apireturn {"name":"type", "type":"int", "desc":"广告类型0只展示1拉起浏览器打开url2App打开3跳到App指定位置", "require":true}
     * @apireturn {"name":"device_type", "type":"int", "desc":"设备类型1ios2android", "require":true}
     * @apireturn {"name":"url", "type":"string", "desc":"跳转链接或位置埋点", "require":true}
     * @apireturn {"name":"image", "type":"string", "desc":"展示图片", "require":true}
     * @example http://amber-frame.com/start/banner
     */
    public function banner()
    {
        $device_type = Input::string('device_type', 'ios');
        $banner = $this->StartModel->getBanner($device_type);
        $this->outResult($banner);
    }
}
