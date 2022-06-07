<?php

namespace app\common\library;

use think\Cache;

/**
 * 缓存数据读写
 */
class CacheDb
{
    /**
     * 从缓存中获取指定表主键数据，没有缓存时从数据库读取并缓存1天
     */
    public static function get($table, $id)
    {
        $data = Cache::get($table . $id);

        if (!$data || !isset($data[$id])) {
            $data = model('app\admin\model\\' . ucfirst($table))->where('id', $id)->find();
            if ($data) {
                self::set($table, $id, $data);
            }
        }



        return $data;
    }

    /**
     * 缓存指定表主键数据
     */
    public static function set($table, $id, $data)
    {
        Cache::set($table . $id, $data, 86400);
    }

    /**
     * 删除缓存数据
     */
    public static function del($table, $id)
    {
        Cache::rm($table . $id);
    }
}
