<?php
namespace zhengcheng\Model;

class getDateInfo
{
    /**
     * 获得今天的日期
     *
     */

    //输入的日期
    public $date;

    //今天日期
    public $day;

    //正负标志
    public $index;

    //相差的天数
    public $diffDate = 0;

    //常规年月份及当月天数
    public $monthAndDay = [
        1  => 31,
        2  => 28,
        3  => 31,
        4  => 30,
        5  => 31,
        6  => 30,
        7  => 31,
        8  => 31,
        9  => 30,
        10 => 31,
        11 => 30,
        12 => 31,
    ];

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function main()
    {
        //获得今天的日期
        $this->getDay();
        //获得日期的日期数组
        $dateArr1 = $this->getDateArr($this->day);
        $dateArr2 = $this->getDateArr($this->date);
        //确定返回的天数是正还是负
        $this->doIndex($dateArr1,$dateArr2);
        if ($this->index === 0)
            exit('日期一样');
        if ($dateArr1[0] > $dateArr2[0]) {
            $min = $dateArr2;
            $max = $dateArr1;
        } else {
            $min = $dateArr1;
            $max = $dateArr2;
        }
        //获得整年所累积的天数
        for ($min[0];$min[0] < $max[0];$min[0]++) {
            //如果是闰年
            if ($this->checkYearIsRun($min[0]))
                $this->diffDate += 366;
            else
                $this->diffDate += 365;
        }
        //减去小年份已过的天数
        $this->diffDate -= $this->getDays($min);
        //加上大年份已过的天数
        $this->diffDate += $this->getDays($max);
        //如果相差天数小于0,只会在同一年时出现
        if ($this->diffDate < 0)
            $this->diffDate = abs($this->diffDate);
        //输出
        $this->toString();
    }

    /**
     * 确定返回正还是负
     * @param $dayArr
     * @param $dateArr
     */
    public function doIndex($dayArr,$dateArr)
    {
        //年大
        if ($dayArr[0] > $dateArr[0]) {
            $index = -1;
        } elseif ($dateArr[0] == $dayArr[0]) {
            if ($dayArr[1] > $dateArr[1]) {
                $index = -1;
            } elseif ($dayArr[1] == $dateArr[1]) {
                if ($dayArr[2] > $dateArr[2]) {
                    $index = -1;
                } elseif ($dayArr[2] == $dateArr[2]) {
                    $index = 0;
                } else {
                    $index = 1;
                }
            } else {
                $index = 1;
            }
        } else {
            $index = 1;
        }
        $this->index = $index;
    }

    /**
     * 检验日期是否符合规范，‘yyyy-mm-dd’
     * 有待完善
     */
    public function checkDate($date)
    {
        $start = strpos($date,'-');
        if (is_bool($start))
            exit('日期格式不正确');
        $last = strrpos($date,'-');
        //获得月份
        @$month = intval(substr($date,$start,$last - $start -1));
        if ($month <= 0 || $month > 12)
            exit('日期格式不正确');
        //获得日期
        $day = intval(substr($date,$last));
        if ($day <=0 || $day > 31)
            exit('日期格式不正确');
        return true;
    }

    //获得今天的日期
    public function getDay()
    {
        $this->day = date('Y-m-d');
    }

    /**
     * 根据日期，获得日期数组
     * @param $date
     * @return mixed
     */
    public function getDateArr($date)
    {
        $start = strpos($date,'-');
        $dateArr[0] = intval(substr($date,0,$start));
        //最后一次出现的位置
        $last = strpos($date,'-',$start + 1);
        $dateArr[1] = intval(substr($date,$start + 1,$last-$start-1));
        $dateArr[2] = intval(substr($date,$last + 1));
        return $dateArr;
    }

    /**
     * 判断是否是闰年
     * @param $year
     * @return bool
     */
    public function checkYearIsRun($year){
        if ($year%4)
            return false;
        if ($year%100 && !$year%400)
            return false;
        return true;
    }

    /**
     * 获取到一个日期数组在该年份已过的天数
     * @param $dateArr
     * @return string
     */
    public function getDays($dateArr)
    {
        $days = $dateArr[2];
        $month = $dateArr[1] - 1;
        for ($i = 1;$i <= $month;$i++) {
            $days += $this->monthAndDay[$i];
        }
        //如果是闰年，再加1
        if ($this->checkYearIsRun($dateArr[0]))
            $days++;
        return $days;
    }


    public function toString()
    {
        if ($this->index > 0) {
            echo $this->diffDate.'天后';
        } elseif ($this->index < 0) {
            echo $this->diffDate.'天前';
        } else {
            echo '就在今天';
        }
    }
}