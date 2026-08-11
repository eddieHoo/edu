<?php
/**
+-------------------------------------------------------------------------------------------------------
* GouGuOPEN [ 左手研发，右手开源，未来可期！]
+-------------------------------------------------------------------------------------------------------
* @Copyright (c) http://blog.gougucms.com All rights reserved.
+-------------------------------------------------------------------------------------------------------
* @Licensed 勾股系列相关开源系统，可免费使用，但并不是自由软件，未经授权许可，不能去除系统的相关版权信息
+-------------------------------------------------------------------------------------------------------
* @Author 勾股工作室 <hdm58@qq.com>
+-------------------------------------------------------------------------------------------------------
*/

namespace app\system\validate;

use think\Validate;

class RuleCheck extends Validate
{
    protected $rule = [
        'title' => 'require',
        'name' => 'require',
        'menu' => 'require',
        'src' => 'unique:admin_rule',
        'id' => 'require',
    ];

    protected $message = [
        'title.require' => '菜单节点名称不能为空',
        'src.unique' => '同样的菜单节点URL已经存在',
        'menu.require' => '是否是左侧菜单需要选择',
        'name.require' => '节点日志操作名称不能为空',
        'id.require' => '缺少更新条件'
    ];

    protected $scene = [
        'add' => ['title','src','name','menu'],
        'edit' => ['id', 'title','src','name','menu']
    ];
}
