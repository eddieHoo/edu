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
namespace app\course\controller;
use app\course\model\Lecturer;
use app\course\model\Material;
use app\course\model\Course;
use app\api\BaseController;
use think\facade\Db;

class Api extends BaseController
{
	//获取分类数据
    public function get_coursecate_tree()
    {
        $cate = get_base_data('CourseCate');
        $list = get_tree($cate, 0, 2);
        $data['trees'] = $list;
        return json($data);
    }
	//获取专家
    public function get_lecturer()
    {
		$param = get_params();
		$where = [];
		if (!empty($param['keywords'])) {
			$where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
		}
		$where[] = ['delete_time', '=', 0];
		$where[] = ['status', '=', 1];
		$model = new Lecturer();
		$list = $model->datalist($where, $param);
		return table_assign(0, '', $list);
    }
	
	//获取课程内容
    public function get_material()
    {
		$param = get_params();
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		
		$where = [];
		if (!empty($param['keywords'])) {
			$where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
		}
		if (!empty($param['types'])) {
			$where[] = ['types', '=', $param['types']];
		}
		if (!empty($param['mids'])) {
			$where[] = ['id', 'notin', $param['mids']];
		}
		if (!empty($param['ids'])) {
			$where[] = ['id', 'in', $param['ids']];
			$param['rows']=99999;
		}
		$where[] = ['delete_time', '=', 0];
		$model = new Material();
		$list = $model->datalist($where, $param);
		return table_assign(0, '', $list);
    }
	
	//获取课程
    public function get_course()
    {
		$param = get_params();
		$where = [];
		if (!empty($param['keywords'])) {
			$where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
		}
		if (!empty($param['cate_id'])) {
			$where[] = ['cate_id', '=', $param['cate_id']];
		}
		if (!empty($param['cids'])) {
			$where[] = ['id', 'notin', $param['cids']];
		}
		if (!empty($param['ids'])) {
			$where[] = ['id', 'in', $param['ids']];
			$param['rows']=99999;
		}
		$where[] = ['delete_time', '=', 0];
		$where[] = ['status', '=', 1];
		$model = new Course();
		$list = $model->datalist($param,$where);
		return table_assign(0, '', $list);
    }
	
	//更新学习时长
    public function duration()
    {
		$param = get_params();
		$second = intval($param['second']);
		$material_id = $param['material_id'];
		$course_id = $param['course_id'];
		$uid = $this->uid;
		//课程内容学习时长更新
		Db::name('MaterialRecord')->where(['uid' => $uid,'course_id' => $course_id,'material_id' => $material_id,'is_finish'=>0,'delete_time' => 0])->update(['duration'=>$second]);
		//课程学习时长更新
		$duration = Db::name('MaterialRecord')->where(['uid' => $uid,'course_id' => $course_id,'delete_time' => 0])->sum('duration');
		Db::name('CourseRecord')->where(['uid' => $uid,'course_id' => $course_id,'delete_time' => 0])->update(['duration'=>$duration]);
        to_assign();
	}
	
	//设置为已完成
    public function finish()
    {
		$param = get_params();
		$material_id = $param['material_id'];
		$course_id = $param['course_id'];
		$uid = $this->uid;
		$integral = Db::name('MaterialRecord')->where(['uid' => $uid,'course_id' => $course_id,'material_id' => $material_id,'delete_time' => 0])->value('integral');
		$res = Db::name('MaterialRecord')->where(['uid' => $uid,'course_id' => $course_id,'material_id' => $material_id,'is_finish'=>0,'delete_time' => 0])->update(['is_finish'=>1,'finish_time'=>time()]);
		if($res !=false){
			//更新学员积分
			incIntegral($uid, $integral,$course_id,$material_id);
			
			//更新课程学习进度
			$course_progress = $this->get_progress($course_id);
			Db::name('CourseRecord')->where(['uid' => $uid,'course_id' => $course_id,'delete_time' => 0])->update(['progress'=>$course_progress]);
			if($course_progress==100){
				//更新学员学分
				$credit = Db::name('CourseRecord')->where(['uid' => $uid,'course_id' => $course_id,'delete_time' => 0])->value('credit');
				incCredit($uid, $credit,$course_id);
			}
			to_assign();
		}
		else{
			to_assign(1,'操作失败');
		}
	}
	
	//获取课程的学习进度
    public function get_progress($course_id=0)
    {
		$uid = $this->uid;
		$material_ids_array = Db::name('CourseSection')->where(['course_id'=>$course_id])->column('mids');
		$material_ids = implode(',',$material_ids_array);
		$material_ids_array = explode(',',$material_ids);
		$material_ids_array = array_unique($material_ids_array);
		$total = count($material_ids_array);
		$finish = Db::name('MaterialRecord')->where([['uid','=',$uid],['course_id','=',$course_id],['material_id','in',$material_ids_array],['is_finish','=',1],['delete_time','=',0]])->count();
		return intval($finish*100/$total);
	}
}
