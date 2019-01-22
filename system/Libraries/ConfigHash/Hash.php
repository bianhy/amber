<?php

namespace Amber\System\Libraries\ConfigHash;

class Hash{

    public static function hash($database, $table, $hash = null)
    {
        $table_alias = '';
        if ($hash) {
            switch ($table) {
                case "user_message"://回复表
                    $table_suffix = substr(md5($hash), 0, 2);
                    $database     = 'example';
                    $table_alias  = 'user_message' . $table_suffix;
                    break;

                case "user_relation": //用户关注表
                case "user_reply":    //用户回帖表
                case "user_topic":    //用户发贴表
                case "user_fans":     //用户粉丝表
                case "user_comment":    //用户评论表（评论文章和视频
                case "user_hobbies":    //用户爱好表
                case "user_concerned"://用户关注表
                case "user_task"://用户任务表
                case "user_collect"://用户收藏表
                    $table_suffix = substr($hash, -2);
                    $database     = 'example'.$table;
                    $table_alias  = $table . '_'. $table_suffix;
                    break;
                default:
                    break;
            }
        }
        return ['database' => $database, 'table_alias' => $table_alias, 'table' => $table];
    }
}