<?php

namespace itd\model;

use itd\NDBI;

class OneMoment
{
    private static $db;
    private $name;
    private $interval;
    private $isPercent;
    private $contentArr;

    public function __construct($name, $interval = 0, $isPercent = false)
    {
        $this->name = $name;
        $this->interval = $interval;
        $this->isPercent = $isPercent;
        if (!self::$db) {
            self::$db = new NDBI([iEnv("DL.DB_HOST"), iEnv("DL.DB_USER"), iEnv("DL.DB_PASS"), iEnv("DL.DB_NAME"), iEnv("DL.DB_PORT", 3306)]);
        }
    }

    public function addContent($content, $title = '')
    {
        $title = $title ? $title . ' => ' : '';
        if (is_object($content)) {
            $content = var_export($content, true);
        } elseif (is_array($content)) {
            $content = json_encode($content, JSON_UNESCAPED_UNICODE);
        }
        $this->contentArr[] = $title . $content;
    }

    public function getContent(): string
    {
        $str = '';
        foreach ($this->contentArr as $content) {
            $str .= $content . "\n\n";
        }
        return $str;
    }

    public function flush()
    {
        $projectName = self::getProjectName();
        $now = date("Y-m-d H:i:s");
        $content = $this->getContent();
        $data = [
            'project' => $projectName,
            'moment_name' => $this->name,
            'content' => $content,
            'record_date' => $now
        ];

        if ($this->interval <= 0) {
            self::$db->insert('_moment_log', $data);
        } elseif ($this->isPercent) {
            if (mt_rand(1, 100) <= $this->interval) {
                self::$db->insert('_moment_log', $data);
            }
        } else {
            $query = "moment_name='" . $this->name . "' AND project='" . $projectName . "'";
            $updateTime = self::$db->out_field('_moment_interval', 'update_time', $query);
            if (!$updateTime) {
                self::$db->insert('_moment_interval', ['project' => $projectName, 'moment_name' => $this->name, 'update_time' => $now]);
            }
            $dif = time() - strtotime($updateTime);
            if (!$updateTime || $dif >= $this->interval) {
                self::$db->insert('_moment_log', $data);
                if ($updateTime) {
                    self::$db->update('_moment_interval', ['update_time' => $now], $query);
                }
            }
        }
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