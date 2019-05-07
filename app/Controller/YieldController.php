<?php
/**
 * PHP性能优化，生成器yield
 * 生成器yield关键字不是返回值，他的专业术语叫产出值，只是生成一个值
 * https://www.cnblogs.com/zuochuang/p/8176868.html
 */
namespace App\Controller;

class YieldController extends AbstractController
{
    //不使用生成器
    public function t1()
    {
        $result = $this->createRange1(10);
        foreach($result as $value){
            sleep(1);//这里停顿1秒，我们后续有用
            echo $value.'<br />';
        }
    }

    //使用生成器
    public function t2()
    {
        $result = $this->createRange2(10); // 这里调用上面我们创建的函数
        foreach($result as $value){
            sleep(1);
            echo $value.'<br />';
        }
    }

    //读取大文件方案
    public function t3()
    {
        $str = $this->readTxt();

        foreach ($str as $key => $value) {
            # code...
            echo $value.'<br />';
        }
    }

    public function readTxt()
    {
        $handle = fopen(dirname(__FILE__).'/test.txt', 'rb');

        while (feof($handle)===false) {
            yield fgets($handle);
        }
        fclose($handle);
    }




    public function createRange1($number){
        $data = [];
        for($i=0;$i<$number;$i++){
            $data[] = time();
        }
        return $data;
    }

    public function createRange2($number){
        for($i=0;$i<$number;$i++){
            yield time();
        }
    }
}
