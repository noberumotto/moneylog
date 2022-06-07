<?php

namespace app\common\library\import;

class Main
{
    public static function handle()
    {
        $isRuning = cache('import_service');

        if ($isRuning) {
            echo "当前忙碌!\r\n";
            return;
        }

        $clistname = 'import_handle_list';
        $clist = cache($clistname);

        if ($clist) {

            echo "开始处理，当前队列共：" . count($clist) . "个文件\r\n";


            $file = $clist[0];

            echo "当前文件：" . $file['file'] . '，用户：' . $file['user_id'];

            $import = new \app\common\library\import\Import($file['file'], $file['user_id']);
            $res = $import->handle();
            if ($res) {
                echo "处理成功！\r\n";
            } else {
                echo "处理失败！\r\n";
            }
            Log::logSave();
        }
    }

    public static function clear()
    {
        $clistname = 'import_handle_list';
        $clist = cache($clistname);

        if ($clist) {
            foreach ($clist as $item) {
                cache($item['user_id'] . '_import_list', null);
            }
        }
        cache('import_service', null);

        cache('import_handle_list', null);

        echo "已清空队列\r\n";
    }
}
