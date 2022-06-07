<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\library\Ems;
use app\common\library\Sms;
use fast\Random;
use think\Config;
use think\Validate;
use app\common\library\CacheDb;
use function fast\array_get;

/**
 * 账单接口
 */
class Moneylog extends Api
{
    protected $noNeedLogin = [];
    protected $noNeedRight = '*';

    protected $model;
    public function _initialize()
    {
        $this->model = model('app\admin\model\Moneylog');
        parent::_initialize();
    }

    /**
     * 获取所有分类
     */
    public function list()
    {
        //  查询类型，day|month

        $type = $this->request->post('type', 'day');

        $date = $this->request->post('date'); // type=day:y-m-d;type=month:y-m

        $where = [];

        $where['user_id'] = $this->auth->id;

        $field = 'id,money,status,time,tags_id,account_id,remark';

        if ($type == 'day') {
            //  按天查询，列出当天所有记录

            $dateArr = [
                $date . ' 00:00:00',
                $date . ' 23:59:59',
            ];

            $list = $this->model->with('tag,account')->whereTime('time', 'between', $dateArr)->where($where)->field($field)->order('time desc')->paginate();

            $totalIn = $this->model->whereTime('time', 'between', $dateArr)->where($where)->where('status', 1)->sum('money');
            $totalOut = $this->model->whereTime('time', 'between', $dateArr)->where($where)->where('status', 0)->sum('money');
        } else if ($type == "month") {
            //  按月查询，汇总当月的每日记录
            $days = date("t", strtotime($date . '-01'));
            $dateArr = [$date . '-01 00:00:00', $date . '-' . $days . ' 23:59:59'];

            $list = $this->model->with('tag,account')->field("FROM_UNIXTIME(time,'%Y-%m-%d') as day")->whereTime('time', 'between', $dateArr)->where($where)->field($field)->order('time desc')->paginate();

            // $list = $this->model->with('tag,account')->whereTime('time', 'between', $dateArr)->where($where)->field("sum(money) as sum,FROM_UNIXTIME(time,'%Y-%m-%d') as day")->group("tags_id,FROM_UNIXTIME(time,'%Y-%m-%d')")->field($field)->order('day desc,id desc')->paginate();

            $totalIn = $this->model->whereTime('time', 'between', $dateArr)->where($where)->where('status', 1)->sum('money');
            $totalOut = $this->model->whereTime('time', 'between', $dateArr)->where($where)->where('status', 0)->sum('money');
        }

        $result = [
            'list' => $list,
            'in' => $totalIn,
            'out' => $totalOut
        ];

        $this->success('', $result);
    }

    public function add()
    {
        // $this->error();
        $data = $this->request->post();
        if (!$data || !is_array($data)) {
            $this->error('参数错误');
        }
        if (count($data) >= 50) {
            $this->error('噢上帝，服务器处理不过来这么多数据');
        }

        // $tagsArr = [];
        // $accountArr = [];

        $trueData = [];
        foreach ($data as &$item) {
            $item['user_id'] = $this->auth->id;
            // $tagsArr[] = $item['tags_id'];
            // $accountArr[] = $item['account_id'];

            //  验证分类
            $category = CacheDb::get('tags', $item['tags_id']);
            if ($category && $category['user_id'] == $this->auth->id) {
                //  验证通过

                //  验证账户
                $account = CacheDb::get('account', $item['account_id']);
                if ($account && $account['user_id'] == $this->auth->id) {

                    //  验证通过
                    array_push($trueData, $item);
                }
            }
        }

        $validate = new \app\admin\validate\Moneylog();
        $vc = $validate->batch()->check($trueData);
        if ($vc) {
            $res = $this->model->allowField(true)->saveAll($trueData);
            $this->success('', $res);
        } else {
            // var_dump($trueData);
            $this->error($validate->getError());
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
                $this->success('ok');
            } else {
                $this->error('删除失败');
            }
        } else {
            $this->error('账户不存在');
        }
    }

    /**
     * 数据统计接口
     */
    public function data()
    {
        $type = $this->request->post('type');
        $date = $this->request->post('date');

        $typeList = ['day', 'week', 'month', 'year'];
        if (!$type || !in_array($type, $typeList) || !$date) {
            $this->error('参数错误');
        }


        //  收入
        $incomeData = array_pad([], 24, 0);

        //  支出
        $outcomeData = array_pad([], 24, 0);

        //  分类信息
        $categoryList = [];

        //  账户信息
        $accountList = [];

        //  收入分类分布数据
        $categoryInData = [];

        //  支出分类分布数据
        $categoryOutData = [];

        //  收入账户分布数据
        $accountInData = [];

        //  支出账户分布数据
        $accountOutData = [];


        switch ($type) {

            case "day":
                //  按日读取
                $dateArr = [
                    $date . ' 00:00:00',
                    $date . ' 23:59:59',
                ];
                $data = $this->model->whereTime('time', 'between', $dateArr)->where('user_id', $this->auth->id)->select();

                //  统计24个时段数据


                foreach ($data as $log) {
                    $hours = date('H', $log['time']);
                    if ($log['status'] == 1) {
                        //  收入
                        $incomeData[intval($hours)] = bcadd($incomeData[intval($hours)], $log['money'], 2);
                    } else {
                        $outcomeData[intval($hours)] = bcadd($outcomeData[intval($hours)], $log['money'], 2);
                    }


                    //  储存分类信息
                    $category_name = '已删除';
                    $category_icon_name = '';
                    if (isset($categoryList[$log['tags_id']])) {
                        $category_name = $categoryList[$log['tags_id']]['name'];
                        $category_icon_name = $categoryList[$log['tags_id']]['icon_name'];
                    } else {
                        $category = model('app\admin\model\Tags')->field('name,icon_name')->where('id', $log['tags_id'])->find();
                        $category_name = $category ? $category['name'] : $category_name;
                        $category_icon_name = $category ? $category['icon_name'] : '';

                        $categoryList[$log['tags_id']] = [
                            'name' => $category_name,
                            'icon_name' => $category_icon_name
                        ];
                    }

                    //  储存账户信息
                    $account_name = '已删除';
                    if (isset($accountList[$log['account_id']])) {
                        $account_name = $accountList[$log['account_id']];
                    } else {
                        $account = model('app\admin\model\Account')->field('name')->where('id', $log['account_id'])->find();
                        $account_name = $account ? $account['name'] : $account_name;
                        $accountList[$log['account_id']] = $account_name;
                    }


                    //  按分类和账户统计数据
                    if ($log['status'] == 1) {
                        //  收入数据

                        //  分类
                        if (isset($categoryInData[$log['tags_id']])) {
                            $categoryInData[$log['tags_id']]['sum'] = bcadd($categoryInData[$log['tags_id']]['sum'], $log['money']);
                        } else {
                            $categoryInData[$log['tags_id']] = [
                                'sum' => $log['money'],
                                'name' => $category_name,
                                'icon_name' => $category_icon_name
                            ];
                        }

                        //  账户
                        if (isset($accountInData[$log['account_id']])) {
                            $accountInData[$log['account_id']]['sum'] = bcadd($accountInData[$log['account_id']]['sum'], $log['money']);
                        } else {
                            $accountInData[$log['account_id']] = [
                                'sum' => $log['money'],
                                'name' => $account_name,
                            ];
                        }
                    } else {
                        //  支出数据
                        if (isset($categoryOutData[$log['tags_id']])) {
                            $categoryOutData[$log['tags_id']]['sum'] = bcadd($categoryOutData[$log['tags_id']]['sum'], $log['money']);
                        } else {
                            $categoryOutData[$log['tags_id']] = [
                                'sum' => $log['money'],
                                'name' => $category_name,
                                'icon_name' => $category_icon_name
                            ];
                        }

                        //  账户
                        if (isset($accountOutData[$log['account_id']])) {
                            $accountOutData[$log['account_id']]['sum'] = bcadd($accountOutData[$log['account_id']]['sum'], $log['money']);
                        } else {
                            $accountOutData[$log['account_id']] = [
                                'sum' => $log['money'],
                                'name' => $account_name,
                            ];
                        }
                    }
                }

                //  收入分类分布
                // $indata = $this->model->whereTime('time', $date)->where('status', 1)->where('user_id', $this->auth->id)->field('sum(money) sum,tags_id')->group('tags_id')->select();
                // foreach ($indata as $log) {

                // }


                break;



            case "week":
                //  按周读取

                $dateArr = explode('~', $date);

                $dateArr[0] .= ' 00:00:00';
                $dateArr[1] .= ' 23:59:59';

                //  收入
                $incomeData = array_pad([], 7, 0);

                //  支出
                $outcomeData = array_pad([], 7, 0);

                //  收入
                $weekInData = $this->model->where('status', 1)->where('user_id', $this->auth->id)->whereTime('time', 'between', $dateArr)->field("FROM_UNIXTIME(time,'%Y-%m-%d') as day,sum(money) as sum")->group("FROM_UNIXTIME(time,'%Y-%m-%d')")->column("sum(money)", "FROM_UNIXTIME(time,'%Y-%m-%d')");

                //  支出
                $weekOutData = $this->model->where('status', 0)->where('user_id', $this->auth->id)->whereTime('time', 'between', $dateArr)->field("FROM_UNIXTIME(time,'%Y-%m-%d') as day,sum(money) as sum")->group("FROM_UNIXTIME(time,'%Y-%m-%d')")->column("sum(money)", "FROM_UNIXTIME(time,'%Y-%m-%d')");

                $startDate = $dateArr[0];

                for ($i = 0; $i < 7; $i++) {
                    $weekDate = date("Y-m-d", strtotime("{$startDate} +{$i} day"));
                    $incomeData[$i] = isset($weekInData[$weekDate]) ? $weekInData[$weekDate] : 0;
                    $outcomeData[$i] = isset($weekOutData[$weekDate]) ? $weekOutData[$weekDate] : 0;
                }
                break;

            case "month":
                //  按月读取
                $days = date("t", strtotime($date . '-01'));
                // $dateArr = [$date . '-01', $date . '-' . $days];
                $dateArr =  [$date . '-01 00:00:00', $date . '-' . $days . ' 23:59:59'];

                //  收入
                $incomeData = array_pad([], $days, 0);

                //  支出
                $outcomeData = array_pad([], $days, 0);

                //  收入
                $indata = $this->model->where('user_id', $this->auth->id)->where('status', 1)->whereTime('time', 'between', $dateArr)->field("FROM_UNIXTIME(time,'%Y-%m-%d') as day,sum(money) as sum")->group("FROM_UNIXTIME(time,'%Y-%m-%d')")->column("sum(money)", "FROM_UNIXTIME(time,'%Y-%m-%d')");

                $outdata = $this->model->where('user_id', $this->auth->id)->where('status', 0)->whereTime('time', 'between', $dateArr)->field("FROM_UNIXTIME(time,'%Y-%m-%d') as day,sum(money) as sum")->group("FROM_UNIXTIME(time,'%Y-%m-%d')")->column("sum(money)", "FROM_UNIXTIME(time,'%Y-%m-%d')");


                for ($i = 0; $i < $days; $i++) {
                    $monthDate = date('Y-m-d', strtotime($date . '-' . ($i + 1)));

                    $incomeData[$i] = isset($indata[$monthDate]) ? $indata[$monthDate] : 0;
                    $outcomeData[$i] = isset($outdata[$monthDate]) ? $outdata[$monthDate] : 0;
                }
                break;
            case "year":
                //  按年读取

                //  获取该年份12月最后一天日期
                $days = date("t", strtotime($date . '12-01'));
                // $dateArr = [$date . '-01-01', $date . '-12-' . $days];
                $dateArr = [$date . '-01-01 00:00:00', $date . '-12-' . $days . ' 23:59:59'];


                //  收入
                $incomeData = array_pad([], 12, 0);

                //  支出
                $outcomeData = array_pad([], 12, 0);

                //  收入
                $indata = $this->model->where('user_id', $this->auth->id)->where('status', 1)->whereTime('time', 'between', $dateArr)->field("FROM_UNIXTIME(time,'%Y-%m') as day,sum(money) as sum")->group("FROM_UNIXTIME(time,'%Y-%m')")->column("sum(money)", "FROM_UNIXTIME(time,'%Y-%m')");

                $outdata = $this->model->where('user_id', $this->auth->id)->where('status', 0)->whereTime('time', 'between', $dateArr)->field("FROM_UNIXTIME(time,'%Y-%m') as day,sum(money) as sum")->group("FROM_UNIXTIME(time,'%Y-%m')")->column("sum(money)", "FROM_UNIXTIME(time,'%Y-%m')");


                for ($i = 0; $i < 12; $i++) {
                    $yearDate = date('Y-m', strtotime($date . '-' . ($i + 1) . '-01'));

                    $incomeData[$i] = isset($indata[$yearDate]) ? $indata[$yearDate] : 0;
                    $outcomeData[$i] = isset($outdata[$yearDate]) ? $outdata[$yearDate] : 0;
                }
                break;
        }

        if ($type == "month" || $type == "week" || $type == "year") {
            //  读取分类分布
            $data = $this->model->where('user_id', $this->auth->id)->whereTime('time', 'between', $dateArr)->field('status,sum(money) as money,tags_id')->group('tags_id')->select();

            foreach ($data as $log) {

                //  储存分类信息
                $category_name = '已删除';
                $category_icon_name = '';
                if (isset($categoryList[$log['tags_id']])) {
                    $category_name = $categoryList[$log['tags_id']]['name'];
                    $category_icon_name = $categoryList[$log['tags_id']]['icon_name'];
                } else {
                    $category = model('app\admin\model\Tags')->field('name,icon_name')->where('id', $log['tags_id'])->find();
                    $category_name = $category ? $category['name'] : $category_name;
                    $category_icon_name = $category ? $category['icon_name'] : '';

                    $categoryList[$log['tags_id']] = [
                        'name' => $category_name,
                        'icon_name' => $category_icon_name
                    ];
                }



                if ($log['status'] == 1) {
                    $categoryInData[$log['tags_id']] = [
                        'sum' => $log['money'],
                        'name' => $category_name,
                        'icon_name' => $category_icon_name
                    ];
                } else {
                    $categoryOutData[$log['tags_id']] = [
                        'sum' => $log['money'],
                        'name' => $category_name,
                        'icon_name' => $category_icon_name
                    ];
                }
            }

            //  读取收入账户分布
            $data = $this->model->where('user_id', $this->auth->id)->whereTime('time', 'between', $dateArr)->field('status,sum(money) as money,account_id')->group('account_id')->select();
            foreach ($data as $log) {
                //  储存账户信息
                $account_name = '已删除';
                if (isset($accountList[$log['account_id']])) {
                    $account_name = $accountList[$log['account_id']];
                } else {
                    $account = model('app\admin\model\Account')->field('name')->where('id', $log['account_id'])->find();
                    $account_name = $account ? $account['name'] : $account_name;
                    $accountList[$log['account_id']] = $account_name;
                }

                if ($log['status'] == 1) {
                    //  账户
                    $accountInData[$log['account_id']] = [
                        'sum' => $log['money'],
                        'name' => $account_name,
                    ];
                } else {
                    $accountOutData[$log['account_id']] = [
                        'sum' => $log['money'],
                        'name' => $account_name,
                    ];
                }
            }
        }



        $this->success('', [
            'incomeList' => $incomeData,
            'outcomeList' => $outcomeData,
            'incategoryData' => array_values($categoryInData),
            'outcategoryData' => array_values($categoryOutData),
            'inAccountData' => array_values($accountInData),
            'outAccountData' => array_values($accountOutData)
        ]);
    }

    public function edit()
    {
        $data = $this->request->post();
        unset($data['tag']);
        unset($data['account']);
        unset($data['time_text']);
        $data['user_id'] = $this->auth->id;

        $log = $this->model->where('id', $data['id'])->where('user_id', $this->auth->id)->count();
        if ($log == 0) {
            $this->error('参数错误');
        }

        $validate = new \app\admin\validate\Moneylog();
        $vc = $validate->check($data);
        if (!$vc) {
            $this->error('参数错误' . $validate->getError());
        }

        //  验证分类
        $category = CacheDb::get('tags', $data['tags_id']);
        if ($category && $category['user_id'] == $this->auth->id) {
            //  验证通过

            //  验证账户
            $account = CacheDb::get('account', $data['account_id']);
            if ($account && $account['user_id'] == $this->auth->id) {

                //  验证通过
                $ret = $this->model->isUpdate(true)->update($data);
                if ($ret) {
                    $this->success('ok');
                } else {
                    $this->error('失败' . $this->model->getError());
                }
            }
        }
        $this->error('参数错误');
    }
}
