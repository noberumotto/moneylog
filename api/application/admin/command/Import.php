<?php

namespace app\admin\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Log;
use think\Db;

class Import extends Command
{

    protected function configure()
    {
        $this->setName('Import')->setDescription('import service');
    }
    /*
     *  开始结束
     * */
    protected function execute(Input $input, Output $output)
    {
        \app\common\library\import\Main::handle();

        echo "处理完成", "\n";
    }
}
