<?php
namespace zhengcheng\Model;

class formatString
{
    public $string;

    public $result = '';

    public $format = ['-','_'];

    public $address = [];

    public function __construct($string)
    {
        $this->string = $string;
    }

    public function main()
    {
        $this->format();
        $this->toString();
    }

    public function format()
    {
        //首字母大写
        $this->string = ucfirst($this->string);
        //获取特殊字符出现的次数
        $count = $this->getCount();
        for ($i = 0;$i < $count;$i++) {
            //获取第一个特殊字符出现的位置
            $first = $this->getFirstAddress();
            //先截取至后一位的字符串
            $substr = substr($this->string,0,$first+2);
            //获取到后半段
            $end = substr($this->string,$first+2);
            //最后一个字符串大写
            $substr = strrev(ucfirst(strrev($substr)));
            //去掉特殊字符
            $substr = substr_replace($substr,null,$first,1);
            //替换掉原字段
            $this->string = $substr.$end;
        }
        $this->result = $this->string;
    }

    /**
     * 获得地址信息
     */
    private function getAddressInfo()
    {
        foreach ($this->format as $val) {
            preg_match_all('/'.$val.'/',$this->string,$this->address[],PREG_OFFSET_CAPTURE);
        }
    }

    /**
     * 获得第一个字符出现的位置
     * @return mixed
     */
    private function getFirstAddress()
    {
        foreach ($this->format as $val) {
            $info = strpos($this->string,$val);
            if (!is_bool($info))
                $address[] = $info;
        }
        sort($address);
        return $address[0];
    }

    /**
     * 获得特殊字符出现的次数
     */
    private function getCount()
    {
        $this->getAddressInfo();
        $this->handleAddress();
        return count($this->address);
    }

    private function handleAddress()
    {
        //只需要具体的地址
        foreach ($this->address as $val) {
            foreach ($val as $secVal) {
                foreach ($secVal as $thirdVal) {
                    $address[] = $thirdVal[1];
                }
            }
        }
        sort($address);
        $this->address = $address;
    }

    public function toString()
    {
        echo $this->result;
        //var_dump($this->address);
    }
}