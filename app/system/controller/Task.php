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

namespace app\system\controller;

use app\base\BaseController;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Task extends BaseController
{
    public function datalist()
    {
        if (request()->isAjax()) {
            $list = Db::name('TimingTask')->where(['delete_time' => 0])->select()->toArray();
			foreach ($list as $key => &$value) {
				$value['tips'] = '每天的 '.sprintf("%02d", $value['plan_hour']).':'.sprintf("%02d", $value['plan_min']).' 执行一次';
				if($value['plan_days'] == 1){
					$value['tips'] = '每隔 '.$value['plan_days'].' 天的'.sprintf("%02d", $value['plan_hour']).':'.sprintf("%02d", $value['plan_min']).' 执行一次';
				}
				if($value['plan_days'] == 2){
					$value['tips'] = '每周的 '.$value['plan_week'] .'的'.sprintf("%02d", $value['plan_hour']).':'.sprintf("%02d", $value['plan_min']).' 执行一次';
				}
				if($value['plan_days'] == 3){
					$value['tips'] = '每月的 '.$value['plan_day'] .'号的'.sprintf("%02d", $value['plan_hour']).':'.sprintf("%02d", $value['plan_min']).' 执行一次';
				}
				if($value['plan_days'] == 4){
					$value['tips'] = '每天0点开始，每隔'.$value['plan_hour'].'小时的第 '.$value['plan_min'].' 分钟执行一次';
				}
				if($value['plan_days'] == 5){
					$value['tips'] = '每天0点开始，每隔 '.$value['plan_min'].' 分钟执行一次';
				}
				if($value['plan_days'] == 6){
					$value['tips'] = '每天0点开始，每隔 '.$value['plan_sec'].' 秒执行一次';
				}
			}
            return to_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
	//新增/编辑模块
    public function add()
    {
		$param = get_params();
        if (request()->isAjax()) {
			if($this->uid!=1){
				return to_assign(1,'只有系统超级管理员才有权限新增或编辑定时任务！');
			}
			$param['start_time'] = strtotime($param['start_time']);
            if (!empty($param['id']) && $param['id'] > 0) {
                Db::name('TimingTask')->where(['id' => $param['id']])->strict(false)->field(true)->update($param);
                add_log('edit', $param['id'], $param);
            } else {
                $tid = Db::name('TimingTask')->strict(false)->field(true)->insertGetId($param);
                add_log('add', $tid, $param);
            }
			return to_assign();
        } else {
			$id = isset($param['id']) ? $param['id'] : 0;
			$detail=[];
			if ($id > 0) {
                $detail = Db::name('TimingTask')->where(['id' => $id])->find();
				$detail['start_time'] = to_date($detail['start_time'],'Y-m-d');
            }
			View::assign('id', $id);
			View::assign('detail', $detail);
            return view();
        }
    }
	
    //删除
    public function del()
    {
		if($this->uid!=1){
			return to_assign(1,'只有超级管理员才有权限删除定时任务！');
		}
        $param = get_params();
		$res = Db::name('TimingTask')->where('id',$param['id'])->update(['delete_time'=>time()]);
		if($res!==false){
			add_log('delete', $param['id'], $param);
			return to_assign();
		}
		else{
			return to_assign(1,'操作失败');
		}
    }
	
}
