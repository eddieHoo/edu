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
use think\facade\Db;
use think\Validate;

class ConfCheck extends Validate
{
	protected function checkOne($value,$rule,$data=[])
	{
		$id = isset($data['id'])?$data['id']:0;
		$count = Db::name('Config')->where([['title','=',$data['title']],['id','<>',$id],['status','>=',0]])->count();
		return $count == 0 ? true : false;
	}
	
	protected function checkOneName($value,$rule,$data=[])
	{
		$id = isset($data['id'])?$data['id']:0;
		$count = Db::name('Config')->where([['name','=',$data['name']],['id','<>',$id],['status','>=',0]])->count();
		return $count == 0 ? true : false;
	}
	
    protected $rule = [
        'title' => 'require|checkOne',
        'name' => 'require|lower|checkOne',
    ];

    protected $message = [
        'title.require' => '配置名称不能为空',
        'title.checkOne' => '同样的配置名称已经存在',
        'name.require' => '配置标识不能为空',
        'name.lower' => '配置标识只能是小写字母',
        'name.checkOne' => '同样的配置标识已经存在',
    ];
}
