<?php

namespace itd;


class DBLog
{
    private static $link;
    private static $sql;
    private static $logLevel;

    private static $table = '_db_log';

    private static function init()
    {
        self::$logLevel = iEnv("DL.LOG_LEVEL");

        if (empty(self::$link)) {
            self::$link = new NDBI([iEnv("DL.DB_HOST"), iEnv("DL.DB_USER"), iEnv("DL.DB_PASS"), iEnv("DL.DB_NAME"), iEnv("DL.DB_PORT", 3306)]);
            self::$link->query("SET NAMES UTF8");
        }
    }

    /**
     * 常规调试日志
     * @param $content
     * @param string $title
     */
    public static function debug($content, $title = '', $trace = false)
    {
        if (self::$logLevel <= 1) {
            self::base_log('debug', $content, $title, $trace);
        }
    }

    /**
     * 常规显示日志
     * @param $content
     * @param string $title
     */
    public static function notice($content, $title = '', $trace = false)
    {
        if (self::$logLevel <= 2) {
            self::base_log('notice', $content, $title, $trace);
        }

    }

    /**
     * 常规警告日志
     * @param $content
     * @param string $title
     */
    public static function warning($content, $title = '', $trace = false)
    {
        if (self::$logLevel <= 3) {
            self::base_log('warning', $content, $title, $trace);
        }

    }

    /**
     * 常规错误日志
     * @param $content
     * @param string $title
     */
    public static function error($content, $title = '', $trace = false)
    {
        if (self::$logLevel <= 4) {
            self::base_log('error', $content, $title, $trace);
        }
    }

    /**
     * 致命错误
     * -监控系统会监控到而且会给发邮件提醒
     */
    public static function fatal($content, $title = '', $trace = false)
    {
        if (self::$logLevel <= 5) {
            self::base_log('fatal', $content, $title, $trace);
        }
    }

    /**
     * 基础日志调用
     * @param $log_type
     * @param $content
     * @param $title
     */
    private static function base_log($log_type, $content, $title, $trace)
    {
        if (empty(self::$link)) {
            self::init();
        }
        if (is_array($content)) {
            if (isset($content[0])) {
                $str = '';
                foreach ($content as $v) {
                    $str .= $v . "\n";
                }
                $content = $str;
            } else {
                $content = json_encode($content, JSON_UNESCAPED_UNICODE);
            }
        }
        if ($trace) {
            $debugInfo = debug_backtrace();
            $content .= ' ：' . addslashes($debugInfo[0]['file']) . ' (' . $debugInfo[0]['line'] . ')';
        }
        $arr = ['project' => self::getProjectName(), 'log_type' => $log_type, 'title' => $title, 'content' => $content, 'record_date' => date('Y-m-d H:i:s')];
        self::$link->insert(self::$table, $arr);
    }

    private static function getProjectName()
    {
        $arr = explode(DIRECTORY_SEPARATOR, __DIR__);
        $flag = false;
        foreach ($arr as $dir) {
            if ($flag) {
                return $dir;
            }
            if ($dir == 'www') {
                $flag = true;
            }
        }
    }
}