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

namespace app\adm\model;
use think\Model;
use think\facade\Db;
class Admin extends Model
{
	// 定义与部门的多对多关联关系
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_admin');
    }
    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function datalist($param,$where,$whereOr)
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        try {
            $list = self::where($where)
			->where(function ($query) use($whereOr) {
				if (!empty($whereOr)){
					$query->whereOr($whereOr);
				}
			})
			->with('departments')
			->order($order)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key){
				$item['position'] ='-';
				$item['department'] ='-';
				$item['department_others'] ='-';
				if($item['id']>1){
					$item['position'] = Db::name('Position')->where('id',$item['position_id'])->value('title');
					$item['department'] = Db::name('Department')->where('id',$item['did'])->value('title');
					//遍历次要部门数据
					$departments = $item['departments']->toArray();
					if(!empty($departments)){
						$item['department_others'] = split_array_field($departments,'title');
					}
				}
				$item['entry_time'] = to_date($item['entry_time'],'Y-m-d');
				$item['last_login_time'] = to_date($item['last_login_time'],'Y-m-d H:i');
				$item['last_login_ip'] = empty($item['last_login_ip']) ? '-' : $item['last_login_ip'];
			});
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }
}
