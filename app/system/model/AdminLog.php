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

declare (strict_types = 1);
namespace app\system\model;

use think\Model;
use think\facade\Db;
use dateset\Dateset;

class AdminLog extends Model
{
    public function get_log_list($param = [])
    {
        $rows = empty($param['limit']) ? get_config('app.pages') : $param['limit'];
        $list = Db::name('AdminLog')
            ->field("a.id,a.uid,a.type,a.subject,a.action,a.create_time,u.name")
			->alias('a')
			->join('Admin u', 'a.uid = u.id')
            ->order('a.create_time desc')
            ->paginate(['list_rows'=> $rows])
			->each(function($item, $key){
				$item['content'] = $item['name']. $item['action'] . '了' . $item['subject'];
				$item['times'] = (new Dateset())->time_trans($item['create_time']);
				return $item;
			});
        return $list;
    }
}
