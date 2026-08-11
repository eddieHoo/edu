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

namespace app\exam\controller;

use app\base\BaseController;
use app\exam\model\Paper as PaperModel;
use app\exam\model\PaperCate as PaperCateModel;
use app\exam\validate\PaperValidate;
use app\exam\validate\PaperCateValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Paper extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new PaperModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$where=[];
			$whereOr=[];
			$where[]=['delete_time','=',0];
            if (!empty($param['keywords'])) {
                $where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
            }
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
            return view();
        }
    }
	
    /**
    * 添加/编辑
    */
    public function add()
    {
		$param = get_params();	
        if (request()->isAjax()) {
			if (isset($param['start_time'])) {
                $param['start_time'] = strtotime($param['start_time']);
            }
            if (isset($param['end_time'])) {
                $param['end_time'] = strtotime($param['end_time']);
				if ($param['end_time'] <= $param['start_time']) {
					return to_assign(1, "结束时间需要大于开始时间");
				}
            }			
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(PaperValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(PaperValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$param['admin_id'] = $this->uid;
                $this->model->add($param);
            }	 
        }else{
			$id = isset($param['id']) ? $param['id'] : 0;
			if ($id>0) {
				$detail = $this->model->getById($id);
				View::assign('detail', $detail);
				return view('edit');
			}
			return view();
		}
    }
	
    /**
    * 查看
    */
    public function view($id)
    {
		$detail = $this->model->getById($id);
		if($detail['types']==1){
			$subjects='全体人员';
			if($detail['open_types']==1){
				$departments = Db::name('Department')->where([['id','in',$detail['dids']]])->column('title');
				$subjects ='部门【'. implode(',' ,$departments).'】';
			}
			if($detail['open_types']==2){
				$positions = Db::name('Position')->where([['id','in',$detail['pids']]])->column('title');
				$subjects ='岗位【'. implode(',' ,$positions).'】';
			}
			if($detail['open_types']==3){
				$unames = Db::name('Admin')->where([['id','in',$detail['uids']]])->column('name');
				$subjects ='员工【'. implode(',' ,$unames).'】';
			}
		}
		if($detail['types']==2){
			$course = Db::name('Course')->where('id',$detail['course_id'])->find();
			$subjects='全体人员';
			if($course['learn_types']==1){
				$departments = Db::name('Department')->where([['id','in',$course['dids']]])->column('title');
				$subjects ='部门【'. implode(',' ,$departments).'】';
			}
			if($course['learn_types']==2){
				$positions = Db::name('Position')->where([['id','in',$course['pids']]])->column('title');
				$subjects ='岗位【'. implode(',' ,$positions).'】';
			}
			if($course['learn_types']==3){
				$unames = Db::name('Admin')->where([['id','in',$course['uids']]])->column('name');
				$subjects ='员工【'. implode(',' ,$unames).'】';
			}
		}
		if($detail['types']==3){
			$train = Db::name('Train')->where('id',$detail['train_id'])->find();
			$subjects='全体人员';
			if($train['types']==1){
				$departments = Db::name('Department')->where([['id','in',$train['dids']]])->column('title');
				$subjects ='部门【'. implode(',' ,$departments).'】';
			}
			if($train['types']==2){
				$positions = Db::name('Position')->where([['id','in',$train['pids']]])->column('title');
				$subjects ='岗位【'. implode(',' ,$positions).'】';
			}
			if($train['types']==3){
				$unames = Db::name('Admin')->where([['id','in',$train['uids']]])->column('name');
				$subjects ='员工【'. implode(',' ,$unames).'】';
			}
		}
		$detail['subjects'] = $subjects;
		$section = Db::name('PaperSection')->where(['paper_id'=>$detail['id']])->select()->toArray();
		foreach ($section as $key => &$value) {
			$value['questions'] = [];
			if(!empty($value['qids'])){
				$value['questions'] = Db::name('Question')
					->field('a.*,c.title as cate_title')
					->alias('a')
					->join('QuestionCate c', 'a.cate_id = c.id')
					->where([['a.id','in',$value['qids']]])
					->select()->toArray();
			}
		}
		
		View::assign('section', $section);
		View::assign('detail', $detail);
		return view();
    }
	
	/**
    * 预览
    */
    public function preview($id)
    {
		$detail = $this->model->getById($id);
		$section = Db::name('PaperSection')->where(['paper_id'=>$detail['id']])->select()->toArray();
		$paper_score = 0;
		if($detail['question_type'] == 1 || $detail['question_type'] == 2){
			$paper_score_array = unserialize($detail['paper_score']);
			foreach ($section as $key => &$value) {
				$value['questions'] = [];
				$num = 0;
				$total_score = 0;
				if(!empty($value['qids'])){
					$questions = Db::name('Question')
						->field('a.*,c.title as cate_title')
						->alias('a')
						->join('QuestionCate c', 'a.cate_id = c.id')
						->where([['a.id','in',$value['qids']]])
						->select()->toArray();
					foreach ($questions as $key2 => &$val) {
						$num++;
						$val['score'] = $paper_score_array[$val['id']];
						$total_score+=$val['score'];
						$val['q_select']=[];
						if(!empty($val['questionselect'])){
							$val['q_select'] = explode("⊥",$val['questionselect']);
						}
					}					
					$value['questions'] = $questions;
					$value['num'] = $num;
					$value['total_score'] = $total_score;
				}
				$paper_score+=$total_score;
			}
		}
		else if($detail['question_type'] == 3){
			foreach ($section as $key => &$value) {
				$value['questions'] = [];
				$num = 0;
				$total_score = 0;
				$map = [];
				$map[] = ['a.delete_time','=',0];
				if(!empty($value['types'])){
					$map[] = ['a.types','=',$value['types']];
				}
				if(!empty($value['cate_id'])){
					$map[] = ['a.cate_id','=',$value['cate_id']];
				}
				if(!empty($value['level'])){
					$map[] = ['a.level','=',$value['level']];
				}
				$questions = Db::name('Question')
					->field('a.*,c.title as cate_title')
					->alias('a')
					->join('QuestionCate c', 'a.cate_id = c.id')
					->where($map)
					->limit($value['score'])
					->orderRaw("RAND()")
					->select()->toArray();
					
				$newquestions = findCombination($questions, $value['score']);
				foreach ($newquestions as $key2 => &$val) {
					$num++;
					$total_score+=$val['score'];
					$val['q_select']=[];
					if(!empty($val['questionselect'])){
						$val['q_select'] = explode("⊥",$val['questionselect']);
					}
				}					
				$value['questions'] = $newquestions;
				$value['num'] = $num;
				$value['total_score'] = $total_score;
				$paper_score+=$total_score;
			}			
		}
		//dd($section);exit;
		View::assign('section', $section);
		View::assign('detail', $detail);
		return view();
    }
	
   /**
    * 删除
    */
    public function del()
    {
		if (request()->isDelete()) {
			$param = get_params();
			$id = isset($param['id']) ? $param['id'] : 0;
			$this->model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    }   
	
	//组卷
    public function question()
    {
        $param = get_params();
        if (request()->isAjax()) {
			if($param['question_type']==1 || $param['question_type']==2){
				$qidsData = isset($param['qids']) ? $param['qids'] : '';
				foreach ($qidsData as $key => $value) {
					if ($value == '') {
						return to_assign(1,'第' . ($key + 1) . '章节的选题未完善');
					}
				}
			}
			if($param['question_type']==3){
				$scoreData = isset($param['score']) ? $param['score'] : 0;
				foreach ($scoreData as $key => $value) {
					if ($value == 0) {
						return to_assign(1,'第' . ($key + 1) . '章节的分数必须大于0');
					}
				}
			}
			
			$paper_id = $param['id'];
			Db::name('PaperSection')->where(['paper_id'=>$paper_id])->delete();			
			//相关内容多个数组;
			$title_data = isset($param['title']) ? $param['title'] : '';
			$qids_data = isset($param['qids']) ? $param['qids'] : '';
			$cate_id_data = isset($param['cate_id']) ? $param['cate_id'] : 0;
			$types_data = isset($param['types']) ? $param['types'] : 0;
			$level_data = isset($param['level']) ? $param['level'] : 0;
			$num_data = isset($param['num']) ? $param['num'] : 0;
			$score_data = isset($param['score']) ? $param['score'] : 0;
			$qids='';		
			$num=0;		
			$score=0;		
			if ($title_data) {
				foreach ($title_data as $key => $value) {
					if (!$value) {
						continue;
					}
					$data = [];
					$data['title'] = $value;
					$data['qids'] = $qids_data[$key];
					$data['cate_id'] = $cate_id_data[$key];
					$data['types'] = $types_data[$key];
					$data['level'] = $level_data[$key];
					$data['num'] = $num_data[$key];
					$data['score'] = $score_data[$key];
					$data['paper_id'] = $paper_id;
					$data['admin_id'] = $this->uid;
					$data['create_time'] = time();
					Db::name('PaperSection')->strict(false)->field(true)->insertGetId($data);
					if(empty($qids)){
						$qids.=$qids_data[$key];
					}
					else{
						$qids.=','.$qids_data[$key];
					}
					$num+=$num_data[$key];
					$score+=$score_data[$key];
				}
			}
			$param['update_time'] = time();
			$param['total_num'] = $num;
			if($param['total_score'] < $param['pass_score']){
				return to_assign(1,'试卷及格分数大于试卷总分，无法保存');
			}
			if($param['total_score'] > $score){
				return to_assign(1,'所有章节的分数总和（'.$score.'）小于试卷总分（'.$param['total_score'].'），无法保存');
			}
			if($param['total_score'] < $score){
				return to_assign(1,'所有章节的分数总和（'.$score.'）大于试卷总分（'.$param['total_score'].'），无法保存');
			}
			$paper_score = Db::name('Question')->where([['id','in',$qids]])->column('score','id');
			$param['paper_qids'] = $qids;
			$param['paper_score'] = serialize($paper_score);
			$res = $this->model->strict(false)->field('id,question_type,total_score,pass_score,total_num,paper_qids,paper_score')->update($param);
			if ($res) {
				add_log('edit', $param['id'], $param);
			}
			return to_assign();			
		}
		else {
			$detail = $this->model->getById($param['id']);
			$section = Db::name('PaperSection')->where(['paper_id'=>$detail['id']])->select()->toArray();
			foreach ($section as $key => &$value) {
				$value['questions'] = [];
				if(!empty($value['qids'])){
					$value['questions'] = Db::name('Question')
						->field('a.*,c.title as cate_title')
						->alias('a')
						->join('QuestionCate c', 'a.cate_id = c.id')
						->where([['a.id','in',$value['qids']]])
						->select()->toArray();
				}
			}
            View::assign('section', $section);
            View::assign('detail', $detail);
            return view();
        }
    }
	
    /**
    * 设置
    */
    public function  check()
    {
		if (request()->isAjax()) {
			$param = get_params();
			if($param['status'] == 0){
				$count = Db::name('PaperRecord')->where(['paper_id'=>$param['id'],'delete_time'=>0])->count();
				if($count>0){
					return to_assign(1, '已有考试记录的试卷，不准下架，如需下架，请先删除考试记录');	
				}
			}
			$res = $this->model->strict(false)->field('id,status')->update($param);
			if ($res) {
				if($param['status'] == 0){
					add_log('disable', $param['id'], $param);
				}
				else if($param['status'] == 1){
					add_log('recovery', $param['id'], $param);
				}
				return to_assign();
			}
			else{
				return to_assign(1, '操作失败');
			}
		} else {
            return to_assign(1, "错误的请求");
        }
    }
   /**
    * 类型数据列表
    */
    public function cate()
    {
        if (request()->isAjax()) {
			$model = new PaperCateModel();
            $list = $model->where('delete_time',0)->order('sort asc')->select();
            return to_assign(0, '', $list);
        }
        else{
            return view();
        }
    }
	
    /**
    * 添加/编辑
    */
    public function cate_add()
    {
		$param = get_params();	
        if (request()->isAjax()) {	
			$model = new PaperCateModel();
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(PaperCateValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$model->edit($param);
            } else {
                try {
                    validate(PaperCateValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $model->add($param);
            }	 
        }else{
			return view();
		}
    }
	
   /**
    * 删除
    */
    public function  cate_del()
    {
		if (request()->isDelete()) {
			$param = get_params();
			$id = isset($param['id']) ? $param['id'] : 0;
			$type = isset($param['type']) ? $param['type'] : 0;
			$model = new PaperCateModel();
			$model->delById($id,$type);
		} else {
            return to_assign(1, "错误的请求");
        }
    }

    /**
    * 设置
    */
    public function  cate_set()
    {
		if (request()->isAjax()) {
			$param = get_params();
			$model = new PaperCateModel();
			$res = $model->strict(false)->field('id,status')->update($param);
			if ($res) {
				if($param['status'] == 0){
					add_log('disable', $param['id'], $param);
				}
				else if($param['status'] == 1){
					add_log('recovery', $param['id'], $param);
				}
				return to_assign();
			}
			else{
				return to_assign(0, '操作失败');
			}
		} else {
            return to_assign(1, "错误的请求");
        }
    }	

}
