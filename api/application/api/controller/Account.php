<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\library\CacheDb;
use app\common\library\Ems;
use app\common\library\Sms;
use fast\Random;
use think\Config;
use think\Validate;
use think\Cache;

/**
 * 账户接口
 */
class Account extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    protected $model;
    public function _initialize()
    {
        $this->model = model('app\admin\model\Account');
        parent::_initialize();
    }


    public function list()
    {
        // $result = $this->model->where('user_id', $this->auth->id)->field('id,name,status')->select();
        $result =  Cache::remember('account_list_' . $this->auth->id, function () {
            return $this->model->where('user_id', $this->auth->id)->field('id,name,status')->select();
        }, 3600);

        $this->success('', $result);
    }

    public function add()
    {
        $name = $this->request->post('name');
        $status = $this->request->post('status');


        if (!$name  || ($status != 0 && $status != 1)) {
            $this->error('参数错误');
        }

        $data = [];
        $data['name'] = $name;
        $data['status'] = $status;
        $data['user_id'] = $this->auth->id;

        $validate = new \app\api\validate\Account();
        $vc = $validate->check($data);
        if ($vc) {
            $category = $this->model->where('status', $status)->where('name', $name)->where('user_id', $this->auth->id)->find();
            if ($category) {
                $this->error('账户已存在哦');
            } else {
                $model = $this->model;
                $res = $model->save($data);
                if ($res) {

                    //  缓存数据
                    CacheDb::set('account', $model->id, $model);
                    $this->updateCache();
                    $this->success('', $model->id);
                } else {
                    $this->error('添加失败');
                }
            }
        } else {
            $this->error('参数错误');
        }
    }

    public function del()
    {
        $id = $this->request->post('id');
        if (!$id) {
            $this->error('参数错误');
        }
        $data = $this->model->where('id', $id)->where('user_id', $this->auth->id)->find();
        if ($data) {
            $res =  $data->delete();
            if ($res) {
                //  更新缓存
                CacheDb::del('account', $id);
                $this->updateCache();
                $this->success('ok');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('账户不存在');
        }
    }

    public function edit()
    {
        $id = $this->request->post('id');
        $name = $this->request->post('name');

        if (!$id) {
            $this->error('参数错误');
        }
        if (!$name) {
            $this->error('没有任何更新');
        }

        $data = [];
        $data['name'] = $name;

        $validate = new \app\api\validate\Account();
        $vc = $validate->check($data);
        if ($vc) {
            $item = $this->model->where('id', $id)->where('user_id', $this->auth->id)->find();
            if ($item) {
                $res =  $item->save($data);
                if ($res) {
                    CacheDb::set('account', $id, $res);

                    $this->updateCache();
                    $this->success('ok');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error('账户不存在');
            }
        } else {
            $this->error('参数错误');
        }
    }

    private function updateCache()
    {
        Cache::set('account_list_' . $this->auth->id, $this->model->where('user_id', $this->auth->id)->field('id,name,status')->select(), 3600);
    }
}
