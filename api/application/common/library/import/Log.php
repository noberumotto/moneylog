<?php

namespace app\common\library\import;

class Log
{
    public static function log($msg, $level = 'info')
    {
        $list = cache('import_libray_log');
        if (!$list || !is_array($list)) {
            $list = [];
        }


        if (count($list) >= 20) {
            self::logSave();
        }
        $time = date('Y-m-d H:i:s');

        $msg = "[{$level}][{$time}] $msg\r\n";
        $msg .= "\r\n-------------------\r\n";

        array_push($list, $msg);

        cache('import_libray_log', $list);
    }

    public static function logSave()
    {
        $list = cache('import_libray_log');

        $dir = ROOT_PATH . 'runtime' . DS . 'log' . DS . 'import' . DS;

        $name = time() . '.log';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if ($list) {
            file_put_contents($dir . $name, implode("\r\n", $list));

            cache('import_libray_log', null);
        }
    }
}
