<?php
/**
+-----------------------------------------------------------------------------------------------
* GouGuOPEN [ 左手研发，右手开源，未来可期！]
+-----------------------------------------------------------------------------------------------
* @Copyright (c) 2021~2024 http://www.gouguoa.com All rights reserved.
+-----------------------------------------------------------------------------------------------
* @Licensed 勾股OA，开源且可免费使用，但并不是自由软件，未经授权许可不能去除勾股OA的相关版权信息
+-----------------------------------------------------------------------------------------------
* @Author 勾股工作室 <hdm58@qq.com>
+-----------------------------------------------------------------------------------------------
*/
declare (strict_types = 1);
namespace app\home\controller;

use app\api\BaseController;
use think\facade\Db;

class api extends BaseController
{
    //首页公告
    public function get_note_list()
    {
        $list = Db::name('Note')
            ->field('a.id,a.title,a.create_time,c.title as cate_title')
            ->alias('a')
            ->join('note_cate c', 'a.cate_id = c.id')
            ->where(['a.status' => 1,'a.delete_time' => 0])
            ->order('a.end_time desc,a.sort desc,a.create_time desc')
            ->limit(8)
            ->select()->toArray();
        foreach ($list as $key => $val) {
            $list[$key]['create_time'] = date('Y-m-d H:i', $val['create_time']);
        }
        $res['data'] = $list;
        return table_assign(0, '', $res);
    }
	
	//首页新闻列表
    public function get_news_list()
    {
		$list = Db::name('News')->where(['delete_time' => 0])->order('id desc')->limit(8)->select()->toArray();
		foreach ($list as $key => $val) {
			$list[$key]['create_time'] = date('Y-m-d H:i', $val['create_time']);
		}
		$res['data'] = $list;
		return table_assign(0, '', $res);
	}
	
    //获取访问记录
    public function get_view_data()
    {
        $param = get_params();
        $first_time = time();
        $second_time = $first_time - 86400;
        $three_time = $first_time - 86400 * 365;
        $begin_first = strtotime(date('Y-m-d', $first_time) . " 00:00:00");
        $end_first = strtotime(date('Y-m-d', $first_time) . " 23:59:59");
        $begin_second = strtotime(date('Y-m-d', $second_time) . " 00:00:00");
        $end_second = strtotime(date('Y-m-d', $second_time) . " 23:59:59");
        $begin_three = strtotime(date('Y-m-d', $three_time) . " 00:00:00");
        $data_first = Db::name('AdminLog')->field('create_time')->whereBetween('create_time', "$begin_first,$end_first")->select();
        $data_second = Db::name('AdminLog')->field('create_time')->whereBetween('create_time', "$begin_second,$end_second")->select();
        $data_three = Db::name('AdminLog')->field('create_time')->whereBetween('create_time', "$begin_three,$end_first")->select();
        return to_assign(0, '', ['data_first' => hour_document($data_first), 'data_second' => hour_document($data_second), 'data_three' => $data_three]);
    }
	
	//获取员工活跃数据
    public function get_view_log()
    {		
        $times = strtotime("-30 day");
        $where = [];
        $where[] = ['uid','<>',1];
        $where[] = ['create_time', '>', $times];
        $list = Db::name('AdminLog')->field("id,uid")->where($where)->select();
        $logs = array();
        foreach ($list as $key => $value) {
            $uid = $value['uid'];
            if (empty($logs[$uid])) {
                $logs[$uid]['count'] = 1;
                $logs[$uid]['name'] = Db::name('Admin')->where('id',$uid)->value('name');
            } else {
                $logs[$uid]['count'] += 1;
            }
        }
        $counts = array_column($logs, 'count');
        array_multisort($counts, SORT_DESC, $logs);
        //攫取前10
        $data_logs = array_slice($logs, 0, 10);
        return to_assign(0, '', ['data_logs' => $data_logs]);
    }
	
	//获取工作日志列表
    public function get_schedule()
    {
		$param = get_params();
		$project_id = isset($param['project_id']) ? $param['project_id'] : 0;
		$task_id = isset($param['task_id']) ? $param['task_id'] : 0;
		if ($project_id>0) {
			$task_ids = Db::name('ProjectTask')->where(['delete_time' => 0, 'project_id' => $project_id])->column('id');
			$where[] = ['a.tid', 'in', $task_ids];
		}
		if ($task_id>0) {
			$where[] = ['a.tid', '=', $task_id];
		}
		$where[] = ['a.delete_time', '=', 0];
		$model = new Schedule();
		$list = $model->datalist($param,$where);
		return table_assign(0, '', $list);
    }
	
    //调整工时
    public function update_schedule()
    {
        $param = get_params();
		$is_leader = isLeader($this->uid);
		$role_auth = isAuth($this->uid,'office_admin','conf_1');
		$hour_auth = valueAuth('office_admin','conf_2');
		$edit_role = false;		
		if($hour_auth==4 && $role_auth > 0){
			$edit_role = true;	
		}
		if($hour_auth==3 && $is_leader > 0){
			$edit_role = true;
		}
		if($hour_auth==2){
			$admin_id = Db::name('Schedule')->where('id',$param['id'])->value('admin_id');
			if($admin_id == $this->uid){
				$edit_role = true;
			}				
		}
		if($edit_role == false){
			return to_assign(1, "您无权限编辑");
		}
		
        if (isset($param['start_time_a'])) {
            $param['start_time'] = strtotime($param['start_time_a'] . '' . $param['start_time_b']);
        }
        if (isset($param['end_time_a'])) {
            $param['end_time'] = strtotime($param['end_time_a'] . '' . $param['end_time_b']);
        }
		if($param['start_time']>time()){
			return to_assign(1, "开始时间不能大于当前时间");		
		}
        if ($param['end_time'] <= $param['start_time']) {
            return to_assign(1, "结束时间需要大于开始时间");
        }
        $where1[] = ['delete_time', '=', 0];
        $where1[] = ['id', '<>', $param['id']];
        $where1[] = ['admin_id', '=', $param['admin_id']];
        $where1[] = ['start_time', 'between', [$param['start_time'], $param['end_time'] - 1]];

        $where2[] = ['delete_time', '=', 0];
        $where2[] = ['id', '<>', $param['id']];
        $where2[] = ['admin_id', '=', $param['admin_id']];
        $where2[] = ['start_time', '<=', $param['start_time']];
        $where2[] = ['start_time', '>=', $param['end_time']];

        $where3[] = ['delete_time', '=', 0];
        $where3[] = ['id', '<>', $param['id']];
        $where3[] = ['admin_id', '=', $param['admin_id']];
        $where3[] = ['end_time', 'between', [$param['start_time'] + 1, $param['end_time']]];

        $record = Db::name('Schedule')
            ->where(function ($query) use ($where1) {
                $query->where($where1);
            })
            ->whereOr(function ($query) use ($where2) {
                $query->where($where2);
            })
            ->whereOr(function ($query) use ($where3) {
                $query->where($where3);
            })
            ->count();
        if ($record > 0) {
            return to_assign(1, "您所选的时间区间已有工作记录，请重新选时间");
        }
        $param['labor_time'] = ($param['end_time'] - $param['start_time']) / 3600;
        $res = Db::name('Schedule')->strict(false)->field(true)->update($param);
        if ($res !== false) {
            return to_assign(0, "操作成功");
            add_log('edit', $param['id'], $param);
        } else {
            return to_assign(1, "操作失败");
        }
    }

}
