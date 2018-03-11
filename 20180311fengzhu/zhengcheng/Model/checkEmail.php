<?php
namespace zhengcheng\Model;

class checkEmail
{
    public $fileName;

    public $string;

    public $contentArr = [];

    public $resultArr = [];

    public $result = '';

    public $flag = false;

    public $resultFileName = 'email.csv';

    public function __construct($fileName,$resultFileName = null)
    {
        $this->fileName = $fileName;
        if ($resultFileName)
            $this->resultFileName = $resultFileName;
        if (!file_exists($fileName))
            exit('文件不存在');
    }



    //read content form fileName
    public function read()
    {
        $this->string = file_get_contents($this->fileName);
    }

    /**
     * 把读取的字符串，按照某个规则分割
     * 返回一个数组
     */
    public function formatString()
    {
        //以换行符为分隔符，分割字符串
        $this->contentArr = explode(PHP_EOL,$this->string);
    }

    public function main()
    {
        //read file
        $this->read();
        //format string
        $this->formatString();
        //handle string
        $this->handle();
        //format string to csv
        $this->handleCsv();
        //write to file
        $this->write();
        //echo result
        $this->toString();

    }

    public function handle()
    {
        foreach ($this->contentArr as $val) {
            $flag = $this->check($val);
            if ($flag) {
                $this->resultArr[] = [$val,'true'];
            } else {
                $this->resultArr[] = [$val,'false'];
            }
        }
    }

    //check string from content of fileName
    public function check($string)
    {
        $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
        return preg_match($pattern,$string);
    }

    /**
     * 写入结果集
     */
    public function write()
    {
        if (file_put_contents($this->resultFileName,$this->result))
            $this->flag = true;
    }

    /**
     * 把数组处理成csv形式的字符串
     */
    public function handleCsv()
    {
        $arr = [];
        foreach ($this->resultArr as $val) {
            $arr[] = $val[0].','.$val[1];
        }
        $this->result = implode(PHP_EOL,$arr);
    }

    public function toString()
    {
        //echo $this->string;
        var_dump($this->flag);
    }



}