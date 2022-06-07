<?php

namespace app\common\library\import\service;

use think\File;
use think\Cache;
use app\common\library\import\Log;

/**
 * 账单导入处理【微信】
 */
class Wechat
{

    //arr[] 0=交易时间,1=交易类型,2=交易对方,3=商品,4=收/支,5=金额(元),6=支付方式,7=当前状态,8=交易单号,9=商户单号,10=备注

    /**
     * 获取支付类型
     * @return int|null 0=支出,1=收入
     */
    public function getStatus($arr)
    {
        if (is_array($arr) && count($arr) < 8) {
            return null;
        }
        if ($arr[4] == '收入') {
            return 1;
        } else if ($arr[4] == '支出') {
            return 0;
        } else {
            if ($arr[4] != '/') {
                Log::log('支付类型获取失败，传入：' . $arr[4], 'error');
            }
            return null;
        }
    }


    /**
     * 获取交易时间
     * @return int|false 时间戳
     */
    public function getTime($arr)
    {
        if (is_array($arr) && count($arr) < 8) {
            return null;
        }
        return  strtotime($arr[0]);
    }

    /**
     * 获取交易金额
     * @return float|int
     */
    public function getMoney($arr)
    {
        if (is_array($arr) && count($arr) < 8) {
            return null;
        }
        return str_replace('¥', '', $arr[5]);
    }

    /**
     * 通过支付方式名称获取账户名
     */
    public function createAccountName($arr)
    {
        if (is_array($arr) && count($arr) < 8) {
            return null;
        }
        $payTypeName = $arr[6];

        try {
            if ($payTypeName == '零钱' || $payTypeName == '/') {
                return '微信支付';
            }
            return mb_substr($payTypeName, 0, 4);
        } catch (\Exception $e) {
            Log::log('账户名称获取失败，传入：' . $payTypeName, 'error');

            return null;
        }
    }


    /**
     * 获取分类名
     * @param mixed $name   交易方名称
     * @param mixed $typeName   交易类型名称
     * @param mixed $statusText 状态文本
     */
    public function createTagName($arr)
    {
        if (is_array($arr) && count($arr) < 8) {
            return null;
        }
        $name = $arr[2];

        $typeName = $arr[1];

        $statusText = $arr[7];

        $log = "\r\n[交易方名称] {$name}\r\n[交易类型名称] {$typeName}\r\n[状态文本] {$statusText}";

        if ($arr[4] == '/') {
            return null;
        }
        if ($typeName == '商户消费'  && $statusText == '支付成功') {
            //  购物类型商户名称
            $shopArr = ['拼多多平台商户', '联华超市', '盒马集市', '京东商城平台商户', '网易严选'];

            //  餐饮
            $eatArr = ['美团平台商户', '美团'];

            //  出行交通
            $trafficArr = ['滴滴出行'];

            //  游戏充值
            $gameArr = ['深圳市腾讯计算机系统有限公司'];

            //  订阅服务
            $subscribeArr = ['上海宽娱'];

            //  快递
            $expressArr = ['顺丰速运', '中国邮政', '中通快递', '圆通速递', '申通快递', '德邦快递', '极兔速递', '德邦快递'];
            if (in_array($name, $shopArr)) {
                return '购物';
            } else  if (in_array($name, $eatArr)) {
                return '餐饮';
            } else  if (in_array($name, $trafficArr)) {
                return '交通出行';
            } else  if (in_array($name, $gameArr)) {
                return '游戏充值';
            } else  if (in_array($name, $subscribeArr)) {
                return '服务订阅';
            } else  if (in_array($name, $expressArr)) {
                return '快递服务';
            } else {
                //  不确定
                Log::log('商户消费分类名称获取失败' . $log, 'error');

                return null;
            }
        } else {

            $tagName = null;

            if ($typeName == '转账'  && $statusText == '对方已收钱') {
                //  转账支出，分类：转账
                $tagName = '转账';
            }

            if ($typeName == '转账'  && $statusText == '已存入零钱') {
                //  转账收入，分类：转账
                $tagName = '转账';
            }

            if ($typeName == '扫二维码付款'  && $statusText == '已转账') {
                //  扫二维码付款，分类：扫码消费
                $tagName = '扫码消费';
            }
            if ($typeName == '二维码收款'  && $statusText == '已收钱') {
                //  扫二维码付款，分类：扫码收款
                $tagName = '扫码收款';
            }
            if ($typeName == '微信红包'  && $statusText == '已存入零钱') {
                //  收到微信红包，分类：微信红包
                $tagName = '红包';
            }

            if ($typeName == '微信红包（群红包）' && $statusText == '支付成功') {
                //  发微信群红包，分类：微信群红包
                $tagName = '红包';
            }

            if ($typeName == '微信红包（单发）'  && $statusText == '支付成功') {
                //  发微信群红包，分类：微信群红包
                $tagName = '红包';
            }

            if (!$tagName) {
                Log::log('分类名称获取失败' . $log, 'error');
            }
            return $tagName;
        }
    }
}
