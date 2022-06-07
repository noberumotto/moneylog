<?php

namespace app\admin\validate;

use think\Validate;

class Moneylog extends Validate
{
    protected $rule = [
        'user_id'  =>  'require|number',
        'money'  =>  'require|number',
        'account_id'  =>  'require|number',
        'tags_id'  =>  'require|number',
        'time'  =>  'require|number',
        'remark'  =>  'max:10',
    ];



    protected $scene = [
        'add'   =>  ['user_id', 'money', 'account_id', 'tags_id', 'time', 'remark'],
        'edit'  =>  ['user_id', 'money', 'account_id', 'tags_id', 'time', 'remark'],
    ];
}
