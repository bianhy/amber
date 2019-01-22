<?php
namespace App\Controller;

use Amber\System\Application;
use Amber\System\Libraries\ApiDoc;
use Amber\System\Libraries\Input;

class DocController extends AbstractController
{

    public function index()
    {
        $api = $service = Input::string('api');
        ApiDoc::parse($api, __NAMESPACE__, Application::$instance);
    }

    /**
     * 显示api提供的所有对外接口
     */
    public function lists()
    {
        $api             = Input::string('api');
        $tmp             = explode('/', trim($api, '/'));
        $controller_name = implode('\\', array_map('ucfirst', $tmp)) . 'Controller';
        $class_name      = __NAMESPACE__ . '\\' . $controller_name;

        $reflector = new \ReflectionClass($class_name);
        echo $reflector->getDocComment() . PHP_EOL;

        $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method_obj) {
            if ($method_obj->class == $class_name) {
                $api_uri = str_replace([__NAMESPACE__ . '\\', 'Controller'], '', $method_obj->class) . '/' . $method_obj->name;
                echo '<a href="/doc/detail/?api='.$api_uri .'">'.$api_uri.'<br>'. PHP_EOL;
            }
        }
    }
}
