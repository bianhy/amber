<?php
/**
 * 等比压缩，生成二维码
 * Created by PhpStorm.
 * Date: 2018/3/6
 * Time: 16:08
 */

namespace App\Controller;


use Amber\System\View;
use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;

class ImageController extends AbstractController
{


    public function index()
    {
        View::show('index');exit;
        echo 'hello php';
    }

    /**
     *
     */
    public function compress()
    {
        $path       = '/images/';
        $pic_name   = '2333';
        $type       = 'jpg';

        // 生成图片的宽度;
        $pic_width  =  0;
        // 生成图片的高度;
        $pic_height =  0;

        if ($_FILES && $_POST) {

            $pic_width  = $_POST['width'];
            $pic_height = $_POST['length'];

            if ($_FILES['image']['size']) {

                $pic_name = date("YmdHis");

                if ($_FILES['image']['type'] == "image/pjpeg" || $_FILES['image']['type'] == "image/jpg" || $_FILES['image']['type'] == "image/jpeg") {
                    $im   = imagecreatefromjpeg($_FILES['image']['tmp_name']);
                    $type = 'jpg';
                } elseif ($_FILES['image']['type'] == "image/png") {
                    $im   = imagecreatefrompng($_FILES['image']['tmp_name']);
                    $type = 'png';
                } elseif ($_FILES['image']['type'] == "image/gif") {
                    $im   = imagecreatefromgif($_FILES['image']['tmp_name']);
                    $type = 'gif';
                } else {
                    $im = false;
                }
                if ($im) {
                    if (file_exists($pic_name . $type)) {
                        unlink($pic_name . $type);
                    }
                    ResizeImage($im, $pic_width, $pic_height, $pic_name, $type);
                    ImageDestroy($im);
                }
            }
        }

        $data['image'] = $path.$pic_name.'.'.$type;

        View::show('image/compress',$data);
    }

    public function qrCode()
    {
        header('Content-Type: image/png');
        $url    = '尊贵六神，奈何情深';
        $qrCode = new BaconQrCodeGenerator();
        echo $qrCode->encoding('UTF-8')
            ->format('png')//格式
            ->size(300)//大小
            ->color(255,0,255)//颜色
            ->backgroundColor(255,255,0)//背景颜色
            ->margin(1)//边距
            ->errorCorrection('H')
            //->merge(dirname(dirname(__FILE__)) . '/www/qrcodes/e1.png')//logo
            ->generate($url);
        exit;
    }
}
