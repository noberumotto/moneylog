<?php

namespace app\api\validate;

use think\Validate;

class Tags extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'name' => 'min:1|max:4',
        'icon_name' => 'in:baby-bottle,baby,candy,cloud,fuel-pump,house,label,man,package,pill,sparkles,woman,airplane,automobile,beach-with-umbrella,beer-mug,bread,briefcase,broccoli,bubble-tea,bus,cat,chart-increasing,clapper-board,cooked-rice,cut-of-meat,fishing-pole,hamburger,handbag,handshake,laptop,party-popper,ping-pong,pizza,service-dog,shopping-cart,sparkling-heart,syringe,t-shirt,taxi,tropical-drink,video-game,watermelon,wrapped-gift,yen-banknote'
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
