<?php
namespace zhengcheng\Model;

class getImg
{
    public $url;

    public $content;

    public $idAddress = [];

    public $id = [];

    public $imgAddress = [];

    private $return = [];

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function main()
    {
        //获得内容
        $this->getContent();
        //获得相关的id地址信息
        $this->getDataGaAction();
        //处理地址信息,活动id
        $this->getId();

        //活动相关url地址信息
        $this->getImgUrl();
        //处理地置信限，获得URL
        $this->getUrl();

        //输出json
        $this->returnJson();
    }

    public function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);//post请求
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    public function getContent()
    {
        //$string = $this->https_request($this->url);
        $string = file_get_contents($this->url);
        $start = strpos($string,'class="photo_grid"');
        $end   = strpos($string,'class="photo_grid"',$start + 1);
        $this->content = substr($string,$start,$end-$start + 1);
    }

    /**
     * 获取到id地址
     */
    public function getDataGaAction()
    {
        //匹配到所有data-ga-action
        preg_match_all('data-ga-action',$this->content,$matches,PREG_OFFSET_CAPTURE);
        //只需要具体的地址
        $this->idAddress = $this->handleAddress($matches);
    }

    public function getId()
    {
        foreach ($this->idAddress as $val) {
            $this->id[] = substr($this->content,$val+15,9);
        }
        //去掉重复的,去掉奇数的
        foreach ($this->id as $key => $val) {
            if (!$key % 2)
                $this->id[] = $val;
        }
        $this->id = array_unique($this->id);
    }

    public function getImgUrl()
    {
        //background-image: url('
        preg_match_all('background-image: url(\'',$this->content,$matches,PREG_OFFSET_CAPTURE);
        $this->imgAddress = $this->handleAddress($matches);
    }

    public function getUrl()
    {
        foreach ($this->imgAddress as $val) {
            $this->url[] = substr($this->content,$val+24,300);
        }
        //匹配到两个冒号之间的所有字符
        foreach ($this->url as $url) {
            $start = strpos($url,'"');
            $end   = strpos($url,'"',$start+1);
            $info[] = substr($url,$start,$end-$start-1);
        }
        //去除健为奇数的
        foreach ($info as $key => $val) {
            if (!$key % 2) {
                $this->url[] = $val;
            }
        }
    }

    /**
     * 处理匹配的数组，只要地址信息
     * @param $matches
     * @return array
     */
    private function handleAddress($matches)
    {
        //只需要具体的地址
        foreach ($matches as $val) {
            foreach ($val as $secVal) {
                foreach ($secVal as $thirdVal) {
                    $address[] = $thirdVal[1];
                }
            }
        }
        return $address;
    }

    public function returnJson()
    {
        //合并两个数组
        foreach ($this->id as $key => $val) {
            $this->return[$key] = [
                'id' => $val,
                'url' => $this->url[$key]
            ];
        }
        header("Content-type:application/json;charset=utf-8");
        exit(json_encode($this->return));
    }

}