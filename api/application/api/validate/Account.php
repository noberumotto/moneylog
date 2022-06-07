<?php

namespace app\api\validate;

use think\Validate;

class Account extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'name' => 'min:1|max:4',
    ];
    /**
     * 提示消息
     */
    protected $message = [];
    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['name'],
        'edit' => ['name'],
    ];
}
