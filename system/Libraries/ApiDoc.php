<?php

namespace Amber\System\Libraries;


use Amber\System\Application;
use Amber\System\Libraries\Http\Request;
use Amber\System\View;

class ApiDoc
{
    public static function parse($service, $namespace=null, Application $application)
    {
        $description = '';
        $descComment = '';

        $typeMaps = array(
            'string'  => '字符串',
            'int'     => '整型',
            'float'   => '浮点型',
            'boolean' => '布尔型',
            'date'    => '日期',
            'array'   => '数组',
            'fixed'   => '固定值',
            'enum'    => '枚举类型',
            'object'  => '对象',
        );

        $service_name = $service;
        $routeInfo    = $application->dispatchDoc($service_name);

        $reflector = new \ReflectionClass($routeInfo[0]);

        $docComment    = $reflector->getMethod($routeInfo[1])->getDocComment();
        $docCommentArr = explode(PHP_EOL, $docComment);
        foreach ($docCommentArr as $comment) {
            $comment   = trim($comment);
            //标题描述
            if (empty($description) && strpos($comment, '@') === false && strpos($comment, '/') === false) {
                $description = substr($comment, strpos($comment, '*') + 1);
                continue;
            }

            //@desc注释
            $pos = stripos($comment, '@desc');
            if ($pos !== false) {
                $descComment = substr($comment, $pos + 5);
                continue;
            }
        }
        preg_match('/@example[\s\t]+(\S*)/m', $docComment, $example);
        $example_url = null;
        $example_ret = null;
        if ($example) {
            $example_url = $example[1];
            $example_ret = self::jsonFormat(Request::get($example_url));
        }

        if (!$example_ret) {
            preg_match('/@example_ret[\s\t]+(\S*)/m', $docComment, $tmp);
            if ($tmp) {
                $example_ret = self::jsonFormat(json_decode($tmp[1], true));
            }
        }

        $rules = [];
        if (preg_match_all('/@apiparam[\s\t]+(\{.*?\})/m', $docComment, $matches)) {
            $rules = array_map(function ($str){
                return json_decode($str, true);
            }, $matches[1]);
        }
        $returns = [];
        if (preg_match_all('/@apireturn[\s\t]+(\{.*?\})/m', $docComment, $matches)) {
            $returns = array_map(function ($str){
                return json_decode($str, true);
            }, $matches[1]);
        }
        View::setViewPath(__DIR__);
        View::show('api_desc_tpl',
            [
                'service'     => $service_name,
                'descComment' => $descComment,
                'description' => $description,
                'typeMaps'    => $typeMaps,
                'rules'       => $rules,
                'returns'     => $returns,
                'example_url' => $example_url,
                'example_ret' => $example_ret,
            ]);
    }


    /** Json数据格式化
     * @param  Mixed $data 数据
     * @param  String $indent 缩进字符，默认4个空格
     * @return JSON
     */
    public static function jsonFormat($data, $indent = null)
    {
        $data = json_encode($data);

        // 将urlencode的内容进行urldecode
        $data = urldecode($data);

        // 缩进处理
        $ret         = '';
        $pos         = 0;
        $length      = strlen($data);
        $indent      = isset($indent) ? $indent : '    ';
        $newline     = "\n";
        $prevchar    = '';
        $outofquotes = true;

        for ($i = 0; $i <= $length; $i++) {
            $char = substr($data, $i, 1);

            if ($char == '"' && $prevchar != '\\') {
                $outofquotes = !$outofquotes;
            } elseif (($char == '}' || $char == ']') && $outofquotes) {
                $ret .= $newline;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $ret .= $indent;
                }
            }

            $ret .= $char;

            if (($char == ',' || $char == '{' || $char == '[') && $outofquotes) {
                $ret .= $newline;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $ret .= $indent;
                }
            }

            $prevchar = $char;
        }

        return $ret;
    }
}