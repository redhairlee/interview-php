<?php
namespace zhengcheng\Model\vote;

class vote_table
{
    public $table = 'vote_table';
    public $vote_table = [
        'id','article_id','ip','d_count','vote_time','uid'
    ];
    public $uid = '';

    public $ip = '';

    public $where;

    private $d_count = 0;

    private $info;

    public function __construct($ip)
    {
        $this->ip = $ip;
        $this->getInfoByIp();
        if (!empty($this->info))
            $this->d_count = $this->info['d_count'];
    }

    /**
     * get d_count by uid
     * @return mixed
     */
    public function getDCount()
    {
        return $this->d_count;
    }

    /**
     * 日投票次数加1
     */
    public function addDCount()
    {
        $this->d_count++;
    }

    /**
     * reset d_count to 1
     */
    public function resetDCount()
    {
        $this->d_count = 1;
    }

    /**
     * 验证是否投了该文章
     * @param $article_id
     * @return bool
     */
    public function verifyVoted($article_id)
    {
        $where = [
            'uid' => $this->uid,'article_id' => $article_id
        ];
        //数据库操作
        return false;
    }

    public function getInfoByIp()
    {
        $where = ['uid' => $this->uid];
        //数据库操作
        $this->info = [];
    }

    //验证和之前操作是否在同一天
    public function verifyToday()
    {
        if (!$this->info['vote_time'])
            return true;
        $today = date('Y-m-d');
        $before = date('Y-m-d',$this->info['vote_time']);
        //同一天，+1
        if ($today === $before)
            $this->addDCount();
        else
            $this->resetDCount();
    }

    //插入一条数据
    public function insert()
    {
        return true;
    }

    public function vote($info)
    {
        //确定d_count
        $this->verifyToday();
        //插入数据库
        return $this->insert();
    }

}