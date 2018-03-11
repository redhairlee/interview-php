<?php
namespace zhengcheng\Model\vote;

class imgCode
{
    private $code;

    private $codeNum;

    private $width;

    private $height;

    private $img;

    public function __construct($codeNum = 4,$width = 100,$height = 80)
    {
        $this->codeNum = $codeNum;
    }

    public function createCode()
    {
        for ($i = 0;$i < $this->codeNum;$i++) {
            $index = rand(0,61);
            $string = '0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
            $this->code .= $string[$index];
        }
    }

    /**
     * 生成图片
     */
    public function createImg()
    {
        $this->img = imagecreate($this->width,$this->height);
        //背景色设置
        $white = imagecolorallocate($this->img,0xFF,0xFF,0xFF);
        imagecolortransparent($this->img,$white);
        //写字
        $black = imagecolorallocate($this->img,0x00,0x00,0x00);
        imagestring($this->img,15,20,20,$this->code,$black);
    }


    public function toString()
    {
        header('Content-type:image/png');
        imagepng($this->img);
    }

    public function getImgCode()
    {
        return $this->code;
    }
}