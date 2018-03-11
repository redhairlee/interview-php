<?php
namespace zhengcheng\Model;

class uploadFile
{

    public $thumb;

    public $imgArr = [
        'jpg','png','jpeg','gif'
    ];

    public $fileInfo;

    public $file;

    public $imgPath = '';

    public $uploadPath = 'upload';

    public $imgInfo;

    public function __construct()
    {
        //创建存储文件夹
        $this->buildImgPath();
        //获得上传资源信息
        $this->getFileInfo();

        //确定上传文件信息
        $this->buildInfo();
        //移动资源文件
        $this->uploadResource();
    }

    public function main()
    {
        //保持为1280*1280
        foreach ($this->imgInfo as $file) {
            $thumb = $this->createThumb($file['destination'],null,1280,1280);
            //生成水印图片
            $water = $this->createWaterImg();
            //合并图片
            $this->mergeImage($water,$thumb);
        }
    }

    public function getFileInfo()
    {
        $this->fileInfo = $_FILES;
        if (empty($this->fileInfo))
            exit('没有文件上传');
    }

    /**
     * 确定上传文件信息
     */
    public function buildInfo()
    {
        foreach ($this->fileInfo as $key => $val) {
            //单文件上传
            if (is_string($val['name'])) {
                $this->file[$key] = $val;
            } else {
                //多文件上传
                foreach ($val['name'] as $secKey => $secVal) {
                    $this->file[$key]['name'] = $secVal;
                    $this->file[$key]['size'] = $val['size'][$secKey];
                    $this->file[$key]['tmp_name'] = $val['tmp_name'][$secKey];
                    $this->file[$key]['error'] = $val['error'][$secKey];
                    $this->file[$key]['type'] = $val['type'][$secKey];
                }
            }
        }
    }

    /**
     * 建立文件路径
     */
    public function buildImgPath()
    {
        //获得今天的日期
        $this->imgPath = date('Y/m/d');
        //确定文件夹是否存在
        if (!file_exists($this->imgPath))
            mkdir($this->imgPath,0777,true);
    }


    /**
     * 获取文件的后缀名
     * @param $fileName
     * @return bool|string
     */
    public function getExt($fileName)
    {
        $location = strrpos($fileName,'.');
        return substr($fileName,$location+1);
    }

    /**
     * 上传资源文件
     */
    public function uploadResource()
    {
        foreach ($this->file as $key => $file) {
            if ($file['error'] !== UPLOAD_ERR_OK)
                exit('文件上传失败');
            $ext = $this->getExt($file['name']);
            if (!in_array($ext,$this->imgArr))
                $this->uploadFile();
            //upload image
            //verify size
            //verify other
            //make the unique name of image
            $fileName = $this->getUniName().'.'.$ext;
            $destination = $this->imgPath.'/'.$fileName;
            //临时文件移动到目的地
            if (move_uploaded_file($file['tmp_name'],$destination)) {
                //旋转相关
                //$destination = $this->checkOrientation($destination);
                $this->imgInfo[$key]['name'] = $fileName;
                $this->imgInfo[$key]['destination'] = $destination;
                unset($file);
            }
        }
    }

    /**
     * 是否需要旋转
     * @param $fileName
     * @return resource
     */
    public function checkOrientation($fileName)
    {
        @$exif = exif_read_data($fileName);
        $image = imagecreatefromstring(file_get_contents($fileName));
        var_dump($fileName);
        if(!empty($exif['Orientation'])) {
            switch($exif['Orientation']) {
                case 8:
                    $image = imagerotate($image,90,0);
                    break;
                case 3:
                    $image = imagerotate($image,180,0);
                    break;
                case 6:
                    $image = imagerotate($image,-90,0);
                    break;
            }
        }
        return $image;
    }

    /**
     * 上传文件
     */
    public function uploadFile()
    {
        exit('上传文件');
    }

    /**
     * 生成唯一文件名
     * @return string
     */
    public function getUniName()
    {
        return md5(uniqid(microtime(true),true));
    }

    /**
     * 生成指定大小的图片
     * @param $fileName
     * @param $destination
     * @param null $dst_w
     * @param null $dst_h
     * @return string
     */
    public function createThumb($fileName,$destination = null,$dst_w = null,$dst_h = null) {
        list($src_w,$src_h,$imgType) = getimagesize($fileName);
        if (is_null($dst_w) || is_null($dst_h)) {
            $dst_w = $src_w * 0.8;
            $dst_h = $src_h * 0.8;
        }
        $mime = image_type_to_mime_type($imgType);      //获得的图像类型的 MIME 类型
        $createFun = str_replace('/','createfrom',$mime);
        $outFun = str_replace('/',null,$mime);
        $src_image = $createFun($fileName);
        $dst_image = imagecreatetruecolor($dst_w,$dst_h);
        imagecopyresampled($dst_image,$src_image,0,0,0,0,$dst_w,$dst_h,$src_w,$src_h);
        if($destination && !file_exists(dirname($destination))){
            mkdir(dirname($destination),0777,true);
        }
        $dstFileName = ($destination == null)?$this->getUniName().'.'.$this->getExt($fileName):$destination;
        $outFun($dst_image,$this->imgPath.'/'.$dstFileName);
        imagedestroy($src_image);
        imagedestroy($dst_image);
        unlink($fileName);
        return $this->imgPath.'/'.$dstFileName;
    }

    /**
     * 创建水印
     * @return resource
     */
    public function createWaterImg()
    {
        $water = imagecreate(100,50);
        //背景色设置
        $white = imagecolorallocate($water,0xFF,0xFF,0xFF);
        imagecolortransparent($water,$white);
        //写字
        $black = imagecolorallocate($water,0x00,0x00,0x00);
        imagestring($water,15,20,20,'chongFei',$black);
        $fileName = $this->imgPath.'/'.$this->getUniName().'.'.'png';
        imagepng($water,$fileName);
        return $fileName;
    }

    /**
     * 活动相应的构造画布函数
     * @param $image
     * @return mixed
     */
    public function getCreateFun($image)
    {
        list($w,$h,$type) = getimagesize($image);
        //获得相应的mime类型
        $mime = image_type_to_mime_type($type);
        //获得相应的构造函数
        unset($w,$h,$type);
        return str_replace('/','createfrom',$mime);
    }

    public function getOutFun($image)
    {
        list($w,$h,$type) = getimagesize($image);
        //获得相应的mime类型
        $mime = image_type_to_mime_type($type);
        //获得相应的构造函数
        unset($w,$h,$type);
        return str_replace('/',null,$mime);
    }

    /**
     * 合并图片
     * @param $dst string 水印等
     * @param $src string 资源图
     */
    public function mergeImage($dst,$src)
    {
        //获得图片的构造函数
        $createFun1 = $this->getCreateFun($dst);
        $createFun2 = $this->getCreateFun($src);
        //分别取到画布中
        $image1 = $createFun1($dst);
        $image2 = $createFun2($src);

        //创建一个和背景图一样的画布
        $image3 = imagecreatetruecolor(imagesx($image2),imagesy($image2));

        //为画布创建白色背景，再设置为透明
        $color = imagecolorallocate($image3, 255, 255, 255);
        imagefill($image3, 0, 0, $color);
        imageColorTransparent($image3, $color);

        //把背景图copy到画布中
        imagecopyresampled($image3,$image2,0,0,0,0,imagesx($image2),imagesy($image2),imagesx($image2),imagesy($image2));
        //水印copy到画布中
        $result = imagecopymerge($image3,$image1,1000,1000,0,0,imagesx($image1),imagesy($image1),100);
        $outFun = $this->getOutFun($src);
        $outFun($image3,$src);
        //unlink($dst);
    }

}