<?php

namespace app\common\library\import;

use think\File;
use think\Cache;
use app\common\library\import\Log;

/**
 * 账单导入处理
 */
class Import
{
    protected $file;

    protected $user_id;

    protected $service;

    protected $fileName;

    public function __construct($file, $user_id)
    {
        $this->fileName = $file;
        $this->file = ROOT_PATH . 'public' . DS . 'uploads' . DS . $file;
        $this->user_id = $user_id;
    }

    public function handle()
    {
        if (!file_exists($this->file)) {
            $this->removeFile();
            Log::log('失败，文件不存在：' . $this->file . 'USERID：' . $this->user_id, 'error');
            return false;
        }

        try {
            $file = new File($this->file, 'r');

            $isStart = false;
            $index = -2;
            $startIndex = 0;

            //  成功处理数量
            $handleCount = 0;
            //  未处理数量
            $unhandleCount = 0;
            //  已处理数据列表
            $list = [];

            //  服务
            $service = $this->service ? $this->service : '\\app\\common\\library\\import\\service\\Wechat';


            //  标记运行中
            cache('import_service', true);


            while (!$file->eof()) {
                $str = $file->fgetcsv()[0];

                if (strstr($str, '微信支付账单明细列表')) {
                    $isStart = true;
                    $startIndex = $index + 1;

                    if (!class_exists($service)) {
                        Log::log('未能找到微信处理类库', 'error');
                        break;
                    }

                    $service = new $service();

                    continue;
                }
                if ($isStart && $index >= $startIndex) {

                    $itemArr = $file->fgetcsv();

                    //  交易类型
                    $status = $service->getStatus($itemArr);


                    //   账户名称
                    $accountName = $service->createAccountName($itemArr);

                    //  金额
                    $money = $service->getMoney($itemArr);


                    //  交易时间
                    $time = $service->getTime($itemArr);


                    //  分类名称
                    $tagName = $service->createTagName($itemArr);



                    if (!empty($tagName) && !empty($accountName)) {
                        //  查找账户
                        $account = $this->getCache('Account', $accountName, $status);
                        //  查找分类id

                        $tag = $this->getCache('Tags', $tagName, $status);

                        if (isset($account['id']) && isset($tag['id'])) {

                            $handleCount++;


                            //  组装数据
                            array_push($list, [
                                'money' => $money,
                                'account_id' => $account['id'],
                                'tags_id' => $tag['id'],
                                'status' => $status,
                                'time' => $time,
                                'user_id' => $this->user_id,
                                'type' => 1,
                                'createtime' => time()
                            ]);
                        }
                    } else {
                        $unhandleCount++;
                    }
                }
                $index++;
            }

            //  写入数据库
            model('app\admin\model\Moneylog')->saveAll($list);

            Log::logSave();

            //  删除文件和清除队列信息
            unlink($this->file);

            $cname = $this->user_id . '_import_list';

            $handleList = cache($cname);
            if (!$handleList) {
                $handleList = [];
            }
            $index = array_search($this->fileName, $handleList);
            if (array_splice($handleList, $index, 1)) {
                cache($cname, $handleList);
            }

            //  更新缓存
            cache('total_import_count_' . $this->user_id, null);
            cache('account_list_' . $this->user_id, null);
            cache('tags_list_' . $this->user_id, null);

            $this->removeFile();


            cache('import_service', null);

            return true;
        } catch (\Exception $e) {
            cache('import_service', null);
            Log::log('无法处理文件：' . $this->file . "\r\n[Exception] " . $e->getMessage() . "\r\n[USERID] " . $this->user_id, 'error');
            $this->removeFile();
            return false;
        }
    }

    /**
     * 从所有待处理队列中移除文件
     */
    private function removeFile()
    {
        //  处理队列
        $clistname = 'import_handle_list';
        $clist = cache($clistname);
        if ($clist) {
            // $clindex = 0;
            foreach ($clist as $key => $item) {
                if ($item['file'] == $this->fileName) {
                    array_splice($clist, $key, 1);
                    break;
                }
            }
            cache($clistname, $clist);
        }
    }

    /**
     * 从缓存中读取账户信息，不存在时创建
     */
    private function getCache($table, $name, $status)
    {
        $cname = $table . '_' . $name . '_' . $status  . '_' . $this->user_id;

        $data = cache($cname);

        if (!$data || !isset($data['id'])) {
            $data = $this->getCreateDb($table, $name, $status);
            cache($cname, $data, 30);
            return $data;
        } else {
            return $data;
        }
    }

    /**
     * 从数据获取指定名称账户信息，不存在时创建
     */
    private function getCreateDb($table, $name, $status)
    {
        $db = model('app\admin\model\\' . $table)->where('user_id', $this->user_id)->where('status', $status)->where('name', $name)->field('id,name,status')->find();
        if ($db) {
            return $db;
        } else {
            $data = [
                'name' => $name,
                'status' => $status,
                'user_id' => $this->user_id,
                'createtime' => time()
            ];
            if ($table == 'Tags') {
                $data['icon_name'] = 'yen-banknote';
            }
            $res = model('app\admin\model\\' . $table)->insert($data);

            return $res;
        }
    }
}
