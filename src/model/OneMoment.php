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
        $this->contentArr[] = $title . $content . "\n\n";

    }

    public function flush()
    {
        $projectName = self::getProjectName();
        $now = date("Y-m-d H:i:s");
        $content = json_encode($this->contentArr, JSON_UNESCAPED_UNICODE);
        $data= [
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
            echo "updateTime:$updateTime<br>";

            if (!$updateTime) {
                self::$db->insert('_moment_interval', ['project' => $projectName, 'moment_name' => $this->name, 'update_time' => $now]);
            }
            echo $dif = time() - strtotime($updateTime);
            echo "$dif >= $this->interval<br>";
            if (!$updateTime || $dif >= $this->interval) {
                echo "need insert<br>";
                self::$db->insert('moment_log', $data);
                echo "insert moment_log ".json_encode($data)."<br>";
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