<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\library\Ems;
use app\common\library\Sms;
use fast\Random;
use think\Config;
use \app\common\library\CacheDb;
use think\Cache;

/**
 * 分类接口
 */
class Tags extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    protected $model;
    public function _initialize()
    {
        $this->model = model('app\admin\model\Tags');
        parent::_initialize();
    }

    /**
     * 获取所有分类
     */
    public function list()
    {
        $result =  Cache::remember('tags_list_' . $this->auth->id, function () {
            return $this->model->where('user_id', $this->auth->id)->field('id,name,icon_name,status')->select();
        }, 3600);

        // $result = $this->model->where('user_id', $this->auth->id)->field('id,name,icon_name,status')->select();
        $this->success('', $result);
    }

    public function add()
    {
        $icon_name = $this->request->post('icon_name');
        $name = $this->request->post('name');
        $status = $this->request->post('status');


        if (!$icon_name || !$name  || ($status != 0 && $status != 1)) {
            $this->error('参数错误');
        }

        $data = [];
        $data['name'] = $name;
        $data['icon_name'] = $icon_name;
        $data['status'] = $status;
        $data['user_id'] = $this->auth->id;

        $validate = new \app\api\validate\Tags();
        $vc = $validate->check($data);
        if ($vc) {
            $category = $this->model->where('status', $status)->where('name', $name)->where('user_id', $this->auth->id)->find();
            if ($category) {
                $this->error('分类已存在哦');
            } else {
                $model = $this->model;
                $res = $model->save($data);
                if ($res) {
                    //  存入缓存
                    CacheDb::set('tags', $model->id, $model);

                    $this->updateCacheList();

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
        $category = $this->model->where('id', $id)->where('user_id', $this->auth->id)->find();
        if ($category) {
            $res =  $category->delete();
            if ($res) {
                //  更新缓存
                CacheDb::del('tags', $id);

                $this->updateCacheList();

                $this->success('ok');
            } else {
                $this->error('更新失败');
            }
        } else {
            $this->error('分类不存在');
        }
    }

    public function edit()
    {
        $id = $this->request->post('id');
        $icon_name = $this->request->post('icon_name');
        $name = $this->request->post('name');

        if (!$id) {
            $this->error('参数错误');
        }
        if (!$icon_name && !$name) {
            $this->error('没有任何更新');
        }

        $data = [];
        if ($name) {
            $data['name'] = $name;
        }
        if ($icon_name) {
            $data['icon_name'] = $icon_name;
        }
        $validate = new \app\api\validate\Tags();
        $vc = $validate->check($data);
        if ($vc) {
            $category = $this->model->where('id', $id)->where('user_id', $this->auth->id)->find();
            if ($category) {
                $res =  $category->save($data);
                if ($res) {
                    //  更新缓存
                    CacheDb::set('tags', $id, $res);

                    $this->updateCacheList();

                    $this->success('ok');
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error('分类不存在');
            }
        } else {
            $this->error('参数错误');
        }
    }

    private function updateCacheList()
    {
        Cache::set('tags_list_' . $this->auth->id, $this->model->where('user_id', $this->auth->id)->field('id,name,icon_name,status')->select(), 3600);
    }
}
