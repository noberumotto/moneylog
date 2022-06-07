<?php

namespace app\api\controller;

use app\common\controller\Api;
use \think\File;
use \think\Cache;


class Import extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';


    protected $tagName;
    protected $accountName;
    protected $status;



    public function clear()
    {

        $result = model('app\admin\model\Moneylog')->where('user_id', $this->auth->id)->where('type', 1)->delete();
        cache('total_import_count_' . $this->auth->id, null);

        $this->success();
    }


    public function list()
    {

        //  服务器状态
        $clistname = 'import_handle_list';
        $clist = cache($clistname);

        $statusText = "空闲";
        if ($clist && is_array($clist)) {
            $count = count($clist);

            if ($count > 50) {
                $statusText = "拥挤";
            } else if ($count > 0) {
                $statusText = "忙碌";
            }
        }


        $importCount =  Cache::remember('total_import_count_' . $this->auth->id, function () {
            return model('app\admin\model\Moneylog')->where('user_id', $this->auth->id)->where('type', '1')->count();
        }, 3600);


        $cname = $this->auth->id . '_import_list';
        $handleList = cache($cname);
        if (!$handleList) {
            $handleList = [];
        }

        foreach ($handleList as $index => &$item) {
            $item = ($index + 1) . ' -> ' . mb_substr($item, 4, 6);
        }
        $this->success('', [
            'import_count' => $importCount,
            'wait_list' => $handleList,
            'server_status' => $statusText
        ]);
    }

    public function upload()
    {
        $file = request()->file('file_file');
        if ($file) {
            $info = $file->rule('sha1')->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {

                $ext = $info->getExtension();

                $name = $info->getSaveName();

                if ($ext != 'csv') {
                    unlink(ROOT_PATH . 'public' . DS . 'uploads' . DS . $name);
                    $this->error('只支持CSV格式文件哦');
                }
                // $sha1 = $info->sha1();

                // $sha1 = mb_substr($info->sha1(), 0, 6);
                $cname = $this->auth->id . '_import_list';

                $handleList = cache($cname);
                if (!$handleList) {
                    $handleList = [];
                }
                if (in_array($name, $handleList)) {
                    cache($cname, null); //  测试代码

                    $this->error('当前账单已经在处理队列中');
                }

                array_push($handleList, $name);
                cache($cname, $handleList);

                //  加入队列
                $clistname = 'import_handle_list';
                $clist = cache($clistname);
                if (!$clist || !is_array($clist)) {

                    $clist = [];
                }
                array_push($clist, [
                    'user_id' => $this->auth->id,
                    'file' => $name
                ]);

                cache($clistname, $clist);


                // $import = new \app\common\library\import\Import($name, $this->auth->id);
                // $import->handle();

                $this->success();
            } else {
                $this->error('无法处理文件');
                // 上传失败获取错误信息
                // echo $file->getError();
            }
        } else {
            $this->error('未上传文件');
            // echo 'no file';
        }
    }
}
