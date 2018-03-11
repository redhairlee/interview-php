<?php
namespace zhengcheng\Model\vote;
require_once 'zhengcheng/Model/vote/imgCode.php';
require_once 'zhengcheng/Model/vote/vote_table.php';

session_start();

date_default_timezone_set('Asia/Shanghai');

class vote
{
    public $voteInfo;

    private static $msg = [
        0   => 'success',
        1   => '数据传输错误',
        3   => '信息不安全',
        4   => '不能对一篇文章投票两次',
        5   => '一天只能投票三次',
        6   => '投票失败',
        7   => '验证码错误或失效',
        8   => '系统错误'
    ];

    public $imgCodeObj;

    public $vote_tableObj;

    private $uid;

    private $ip;

    private $codeExpire = 120;      //秒

    public function __construct()
    {
        $this->voteInfo = $_POST;
        if (empty($this->voteInfo))
            $this->returnJson(1);
        //instance of imgCode class
        $this->imgCodeObj = new imgCode();

        @$this->ip = $this->voteInfo['ip'];
        if (empty($this->ip))
            $this->returnJson(3);
        //instance of vote_table class
        $this->vote_tableObj = new vote_table($this->voteInfo['ip']);
    }

    private function createUid()
    {

    }

    //get imgCode
    public function createImgCode()
    {
        //输出图片
        $this->imgCodeObj->toString();

        //存入session中
        $_SESSION[$this->ip]['codeExpire'] = time();
        $_SESSION[$this->ip]['imgCode']    = $this->imgCodeObj->getImgCode();

        session_write_close();
    }

    //verify imgCode
    public function verifyImgCode()
    {
        if (!isset($_SESSION[$this->ip]) || empty($_SESSION[$this->ip]))
            $this->returnJson(3);
        if (!($this->voteInfo['imgCode'] == $_SESSION[$this->ip]['imgCode'] && time() < $_SESSION[$this->ip]['codeExpire'] + $this->codeExpire))
            $this->returnJson(7);
    }

    //vote
    public function vote()
    {
        //verify imgCode
        $this->verifyImgCode();

        //verify voteInfo
        $this->verifyInfo();

        //vote
        $return = $this->vote_tableObj->vote($this->voteInfo);

        //vote success
        if ($return && is_bool($return))
            $this->returnJson(0);
        //failed
        $this->returnJson(6);
    }

    //verify info before vote
    public function verifyInfo()
    {
        //验证一个人今天是否已投票三次
        $this->verifyDayCount();
        $this->verifyVoted();
    }

    //验证一个人今天是否已投票三次
    public function verifyDayCount()
    {
        $d_count = $this->vote_tableObj->getDCount();
        if ($d_count >2)
            $this->returnJson(5);
    }

    //验证是否对一篇文章已投票
    public function verifyVoted()
    {
        if ($this->vote_tableObj->verifyVoted($this->voteInfo['article_id']))
            $this->returnJson(4);
    }

    /**
     * 输出json数据
     * @param $status
     */
    public function returnJson($status)
    {
        header("Content-type:application/json;charset=utf-8");
        if (self::$msg[$status]) {
            $msg = [
                'status' => intval($status),
                'msg'    => self::$msg[$status]
            ];
        } else {
            $msg = [
                'status' => 8,
                'msg'    => self::$msg[8]
            ];
        }
        $msg = json_encode($msg);
        exit($msg);
    }



}