<?php

namespace itd;

use itd\model\OneMoment;

/** 瞬间捕抓日志 */
class MMLog
{
    /** @var OneMoment[] */
    static private $instances = [];
    /** @var OneMoment */
    static private $lastInstance;

    /**
     * 创建一个瞬间
     * @param string $momentName 瞬间的名称
     * @param int $interval 按间隔记录(如果是百分比 1-100)
     * @param bool $isPercent
     */
    static public function createMoment(string $momentName, int $interval = 0, bool $isPercent = false)
    {
        if (!self::$instances[$momentName]) {
            self::$instances[$momentName] = new OneMoment($momentName, $interval, $isPercent);
        }
        self::$lastInstance = self::$instances[$momentName];

    }

    static public function addContent($content, $title = '')
    {
        if (self::$lastInstance) {
            self::$lastInstance->addContent($content, $title);
        }
    }


    /** 往瞬间里面增加内容 */
    static public function addMomentContent($momentName, $content, $title = '')
    {
        $moment = self::$instances[$momentName];
        if ($moment) {
            $moment->addContent($content, $title);
        }
    }


    /** 判断记录间隔,判断是否需要记录 */
    static public function flushMoment($momentName = '')
    {
        if ($momentName) {
            $moment = self::$instances[$momentName];
            $moment->flush();
        } else {
            foreach (self::$instances as $moment) {
                $moment->flush();
            }
        }

    }


}

