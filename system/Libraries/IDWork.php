<?php


namespace Amber\System\Libraries;
/**
 * ID 生成策略
 * 毫秒级时间41位+机器ID 10位+毫秒内序列12位。
 * 第一位为未使用（实际上也可作为long的符号位）
 * 0---0000000000 0000000000 0000000000 0000000000 0 --- 00000 ---00000 ---0000000000 00
 *  41bits是以微秒为单位的timestamp
 *  5bits datacenter标识位
 *  5bits worker 标识。
 *  最后12bits是累加计数器。
 *  标明最多只能有1024台机器同时产生ID，sequence number(12bits)也标明1台机器1ms中最多产生4096个ID，
 */
class IDWork
{
    const TWE_POCH           = 1467875407204;
    //机器标识位数
    const WORKER_ID_BITS     = 5;
    //数据中心标识位数
    const DATACENTER_ID_BITS = 5;
    //毫秒内自增位
    const SEQUENCE_BITS      = 12;

    //机器ID偏左移位
    public $workerIdShift = 0;
    //数据中心ID左移位
    public $datacenterIdShift = 0;
    //时间毫秒左移位
    public $timestampLeftShift = 0;

    //最大机器标识数
    public $maxWorkerId = 0;
    //最大数据中心数
    public $maxDatacenterId = 0;
    //毫秒里最大计数
    public $sequenceMax = 0;

    public static $lastTimestamp = -1;
    public static $sequence = 0;
    public static $workerId;
    public static $datacenterId;

    public function __construct($workId=0, $datacenterId=0)
    {
        $this->maxWorkerId     = -1 ^ (-1 << self::WORKER_ID_BITS);
        $this->maxDatacenterId = -1 ^ (-1 << self::DATACENTER_ID_BITS);
        $this->sequenceMax     = -1 ^ (-1 << self::SEQUENCE_BITS);

        $this->workerIdShift      = self::SEQUENCE_BITS;
        $this->datacenterIdShift  = self::SEQUENCE_BITS + self::WORKER_ID_BITS;
        $this->timestampLeftShift = self::SEQUENCE_BITS + self::WORKER_ID_BITS + self::DATACENTER_ID_BITS;

        if ($workId > $this->maxWorkerId || $workId < 0) {
            throw new \Exception("worker Id can't be greater than " . $this->maxWorkerId . " or less than 0");
        }

        if ($datacenterId > $this->maxDatacenterId || $datacenterId < 0) {
            throw new \Exception("worker Id can't be greater than " . $this->maxDatacenterId . " or less than 0");
        }

        self::$workerId     = $workId;
        self::$datacenterId = $datacenterId;
    }


    /**
     * 获得当前时间戳的毫秒
     */
    public function timeGen()
    {
        return sprintf("%.3f",microtime(true)) * 1000;
    }

    public function tilNextMillis($lastTimestamp)
    {
        $timestamp = $this->timeGen();
        while ($timestamp <= $lastTimestamp) {
            $timestamp = $this->timeGen();
        }

        return $timestamp;
    }

    public function nextId()
    {
        $timestamp = $this->timeGen();
        if (self::$lastTimestamp == $timestamp) {
            self::$sequence = (self::$sequence + 1) & $this->sequenceMax;
            if (self::$sequence == 0) {
                $timestamp = $this->tilNextMillis(self::$lastTimestamp);
            }
        } else {
            self::$sequence = 0;
        }
        if ($timestamp < self::$lastTimestamp) {
            throw new \Exception("Clock moved backwards.  Refusing to generate id for " . (self::$lastTimestamp - $timestamp) . " milliseconds");
        }

        self::$lastTimestamp = $timestamp;

        return (($timestamp - self::TWE_POCH) << $this->timestampLeftShift) |
        (self::$datacenterId << $this->datacenterIdShift) |
        (self::$workerId << $this->workerIdShift) |
        self::$sequence;
    }

    public static function get($workId=0, $datacenterId=0)
    {
        return (new self($workId, $datacenterId))->nextId();
    }
}