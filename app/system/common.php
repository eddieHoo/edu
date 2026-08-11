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

use think\facade\Db;

//读取后台菜单列表
function admin_menu()
{
    $menu = Db::name('AdminRule')->where(['menu' => 1,'status'=>1])->order('sort asc,id asc')->select()->toArray();
    return $menu;
}

//读取权限节点列表
function admin_rule()
{
    $rule = Db::name('AdminRule')->where(['status'=>1])->order('sort asc,id asc')->select()->toArray();
    return $rule;
}

//读取指定权限分组菜单详情
function admin_group_info($id)
{
    $rule = Db::name('AdminGroup')->where(['id' => $id])->value('rules');
	$rules = explode(',', $rule);
    return $rules;
}

//读取功能模块列表
function admin_module()
{
    $module = Db::name('AdminModule')->order('id asc')->select()->toArray();
    return $module;
}

//定时任务执行类型
function get_plan_types()
{
	$types_array = ['每天','每N天','每周','每月','每N时','每N分钟','每N秒'];
	return $types_array;
}

function plan_types_name($types=0)
{
	$types_array = get_plan_types();
	return $types_array[$types];
}

//获取下一次执行的时间
function get_next_time($types,$days,$week,$day,$hour,$min)
{
	$newDate = '';
	if($types==0){
		return 0;
	}
	if($types == 1){
		// 获取当前日期的时间戳
		$currentTimestamp = time();
		// 计算明天的时间戳
		$newTimestamp = $currentTimestamp + (60 * 60 * 24);
		$newDate = date('Y-m-d',$newTimestamp);
	}
	else if($types == 2){
		// 获取当前日期的时间戳
		$currentTimestamp = time();
		// 计算N天的时间戳
		$newTimestamp = $currentTimestamp + (60 * 60 * 24 * $days);
		$newDate = date('Y-m-d',$newTimestamp);
	}
	else if($types == 3){
		// 创建一个DateTime对象表示当前日期和时间
		$now = new DateTime();
		// 获取当前日期是星期几（返回0=星期日, 1=星期一, ..., 6=星期六）
		$dayOfWeek = $now->format('w');
		// 计算下周几的日期（星期三是3，所以我们需要加上差值）
		$daysToAdd = ($week - $dayOfWeek + 7) % 7; // 加上7是为了处理$dayOfWeek小于3的情况
		// 修改日期
		$nextWednesday = clone $now;
		$nextWednesday->modify("+$daysToAdd days");

		// 获取下周几的时间戳
		$newTimestamp = $nextWednesday->getTimestamp();
		$newDate = date('Y-m-d',$newTimestamp);
	}
	else if($types == 4){
		// 创建一个 DateTime 对象表示当前日期
		$date = new DateTime();
		$currentYear = $date->format('y');
		$currentMonth = $date->format('m');
		$currentDay = $date->format('d');
		$currentTotalDays = $date->format('t');
		
		if($day>$currentDay){
			if($day<=$currentTotalDays){
				$newDate = $currentYear.'-'.$currentMonth.'-'.$day;
			}
			else{
				$newDate = $currentYear.'-'.$currentMonth.'-'.$currentTotalDays;
			}
		}
		else{
			// 修改日期到下个月的第一天
			$date->modify('next month first day');
			// 获取下个月的最后一天
			$date->modify('last day of this month');
			// 使用 format 方法的 't' 格式化选项来获取当月的天数
			$year = $date->format('Y');
			$month = $date->format('m');
			$totalDays = $date->format('t');
			
			if($day<=$totalDays){
				$newDate = $year.'-'.$month.'-'.$day;
			}
			else{
				$newDate = $year.'-'.$month.'-'.$totalDays;
			}
		}
	}
	return strtotime($newDate.' '.$hour.':'.$min.':00');
}