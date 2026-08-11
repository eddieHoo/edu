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

use app\base\BaseController;
use app\course\model\Course as CourseModel;
use app\course\model\CourseRecord;
use app\course\validate\CourseCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Learn extends BaseController
{	
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new CourseModel();
    }
	//我的学习列表
	public function datalist()
    {
        if (request()->isAjax()) {
            $param = get_params();
			$uid = $this->uid;
			$did = $this->did;
			$pid = $this->poid;
			$where=[];
			$whereOr=[];
			$map1=[
				['learn_types','=',0]
			];
			$map2=[
				['learn_types','=',1],
				['', 'exp', Db::raw("FIND_IN_SET('{$did}',dids)")]
			];
			$map3=[
				['learn_types','=',2],
				['', 'exp', Db::raw("FIND_IN_SET('{$pid}',pids)")]
			];
			$map4=[
				['learn_types','=',3],
				['', 'exp', Db::raw("FIND_IN_SET('{$uid}',uids)")]
			];
			$whereOr =[$map1,$map2,$map3,$map4];
			if (!empty($param['keywords'])) {
                $where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['cate_id'])) {
                $where[] = ['cate_id', '=', $param['cate_id']];
            }
			if (!empty($param['types'])) {
                $where[] = ['types', '=', $param['types']];
            }
			$where[] = ['delete_time', '=', 0];
			$where[] = ['status', '=', 1];
            $param['uid'] = $uid;
			$list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
    //查看
    public function view()
    {
        $param = get_params();
        $id = isset($param['id']) ? $param['id'] : 0;
        $detail = $this->model->getById($id);
        if (empty($detail)) {
            return to_assign(1, '课程不存在');
        } else {
			$uid=$this->uid;
			$section = Db::name('CourseSection')->where(['course_id'=>$detail['id']])->select()->toArray();
			foreach ($section as $key => &$value) {
				$value['material'] = [];
				if(!empty($value['mids'])){
					$ids = $value['mids'];
					$exp = ("field(id,".$ids.")");
					$material = Db::name('Material')->where([['id','in',$ids]])->orderRaw($exp)->select()->toArray();
					foreach ($material as $key => &$val) {
						$val['is_finish'] = 2;
						$material_finish = Db::name('MaterialRecord')->where(['uid'=>$uid,'course_id'=>$param['id'],'material_id'=>$val['id'],'delete_time'=>0])->find();
						if(!empty($material_finish)){
							$val['is_finish'] = $material_finish['is_finish'];
						}
					}
					$value['material'] = $material;
				}
			}
			
			$record = Db::name('CourseRecord')->where(['uid'=>$uid,'course_id'=>$detail['id'],'delete_time'=>0])->find();
			if(!empty($record)){
				$material_record = Db::name('MaterialRecord')
					->field('mr.*,m.title')
					->alias('mr')
					->join('Material m','m.id = mr.material_id','LEFT')
					->where(['mr.uid'=>$uid,'mr.course_id'=>$detail['id'],'mr.delete_time'=>0])->order('mr.update_time desc')->limit(1)->select();
				$record['material_record'] = $material_record[0];
			}
			
			View::assign('detail', $detail);
			View::assign('record', $record);
            View::assign('section', $section);
            View::assign('detail', $detail);
            return view();
        }
    }
	
	//学习
    public function learn()
    {
		$param = get_params();
		/*
		if($this->corese_role($param['id'])==false){
			return view('role');
		}
		*/
		$detail = $this->model->getById($param['id']);
		$learn = Db::name('Material')->where('id',$param['mid'])->find();
		
		$section = Db::name('CourseSection')->where(['course_id'=>$detail['id']])->select()->toArray();
		foreach ($section as $key => &$value) {
			$value['material'] = [];
			if(!empty($value['mids'])){
				$ids = $value['mids'];
				$exp = ("field(id,".$ids.")");
				$material = Db::name('Material')->where([['id','in',$ids]])->orderRaw($exp)->select()->toArray();
				foreach ($material as $key => &$val) {
					$val['type_str'] = content_types_name($val['types']);
				}
				$value['material'] = $material;
			}
		}
		
		$uid = $this->uid;
		$is_finish = 0;
		//添加学习记录
		$course_record = Db::name('CourseRecord')->where(['uid'=>$uid,'course_id'=>$param['id'],'delete_time'=>0])->count();
		$material_record = Db::name('MaterialRecord')->where(['uid'=>$uid,'course_id'=>$param['id'],'material_id'=>$param['mid'],'delete_time'=>0])->count();
		if($course_record == 0){
			$course_data = [
				'uid' => $uid,
				'course_id' => $detail['id'],
				'credit' => $detail['credit'],
				'create_time' => time(),
			];
			Db::name('CourseRecord')->strict(false)->field(true)->insertGetId($course_data);
		}
		if($material_record == 0){
			$material_data = [
				'uid' => $uid,
				'course_id' => $detail['id'],
				'material_id' => $learn['id'],
				'integral' => $learn['integral'],
				'create_time' => time(),
				'update_time'=>time()
			];
			Db::name('MaterialRecord')->strict(false)->field(true)->insertGetId($material_data);
		}
		else{
			Db::name('MaterialRecord')->where(['uid'=>$uid,'course_id'=>$param['id'],'material_id'=>$param['mid'],'delete_time'=>0])->update(['update_time'=>time()]);
			$is_finish = Db::name('MaterialRecord')->where(['uid'=>$uid,'course_id'=>$param['id'],'material_id'=>$param['mid'],'delete_time'=>0])->value('is_finish');
		}
		
		$host = "//".$_SERVER ['HTTP_HOST'];
        View::assign('host', $host);
		//
		View::assign('detail', $detail);
		View::assign('learn', $learn);
        View::assign('section', $section);
        View::assign('mid', $param['mid']);
        View::assign('is_finish', $is_finish);
        View::assign('section_count', count($section));
		//1视频,2PDF,3WORD,4PPT,6网页(链接),5图文
		if($learn['types'] == 1){
			return view('video');
		}
		else{
			return view('content');
		}
    }
	
	//判断课程是否有权限学习
    public function corese_role($course_id=0)
    {
		$uid = $this->uid;
		$did = $this->did;
		$pid = $this->pid;
		$is_open = Db::name('Course')->where(['id'=>$course_id])->value('is_open');
		//公开的课程
		if($is_open == 1){
			return true;
		}
		
		//岗位课
		$where1 = [];		
        $where1[] = ['status', '=', 1];
		$where1[] = ['delete_time', '=', 0];
		$where1[] = ['', 'exp', Db::raw("FIND_IN_SET({$pid},pids)")];
		$course_ids_array = Db::name('PositionLearning')->where($where1)->column('course_ids');
		
		//计划课
        $where = [];
		$map1 = [];
        $map2 = [];
        $map3 = [];
        $map4 = [];
		
        $where[] = ['status', '=', 1];
		$where[] = ['delete_time', '=', 0];
		
		$map1[] = ['open_type', '=', 0];
		
		$map2[] = ['open_type', '=', 1];
		$map2[] = ['', 'exp', Db::raw("FIND_IN_SET({$uid},uids)")];
		
		$map3[] = ['open_type', '=', 2];
		$map3[] = ['', 'exp', Db::raw("FIND_IN_SET({$did},dids)")];
		
		$map4[] = ['open_type', '=', 3];
		$map4[] = ['', 'exp', Db::raw("FIND_IN_SET({$pid},pids)")];
		
		$whereOr =[$map1,$map2,$map3,$map4];
		$learnings = Db::name('Learning')
		->where(function ($query) use ($whereOr) {// 注意此处是where ,里面是whereOr才能实现第一部分是where的情况,参考下图中的create_by
			if (!empty($whereOr))
				$query->whereOr($whereOr);
		})
		->select()->toArray();
		
		foreach ($learnings as $key => &$val) {
			$cids_array = Db::name('LearningStep')->where(['learning_id'=>$val['id']])->column('cids');
			$course_ids_array = array_merge($cids_array, $course_ids_array);
		}
		$course_ids = implode(',',$course_ids_array);
		$course_ids_array = explode(',',$course_ids);
		$course_ids_array = array_unique($course_ids_array);
		//var_dump($course_ids_array);
		if (in_array($course_id, $course_ids_array)){
			return true;
		}
		else{
			return false;
		}
	}
}
