<?php

function strip_content_tags($content)
{
    return trim(strtr(strip_tags($content, '<p><br>'),
        array(
            '<br />' => '<br/>',
            '</p>' => '',
            '&nbsp;' => ' ',
            '&middot;' => '˙',
            '\r' => ''
        ) ));

}

function avatar_add_domain($file)
{
    if (!$file) {
        return 'http://p'.mt_rand(1,8).'.amber.com/icon.png';
    }

    if(!preg_match('/^http/', $file) ) {
        return 'http://p'.mt_rand(1,8).'.amber.com/'.$file;
    } else {
        return $file;
    }
}

/**
 * 用户头像处理
 * @param $userInfo
 * @return array
 */
function users_avatar_domain($userInfo){

    if (isset($userInfo['uid'])) {//单个用户
        $userInfo = [$userInfo];
    }

    foreach ($userInfo as &$row) {
        $row           = array_remove_keys($row,['username', 'password', 'mobile', 'birthday']);
        $row['avatar'] = avatar_add_domain($row['avatar']);
    }
    return $userInfo;
}
