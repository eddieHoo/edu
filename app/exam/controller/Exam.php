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
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Exam extends BaseController
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
			$did = $this->did;
			$pid = $this->poid;
			$uid = $this->uid;
			$where=[];
			$whereOr=[];
			if($uid>1){
				$map1=[
					['types','=',1],
					['open_types','=',0]
				];
				$map2=[
					['types','=',1],
					['open_types','=',1],
					['', 'exp', Db::raw("FIND_IN_SET('{$did}',dids)")]
				];
				$map3=[
					['types','=',1],
					['open_types','=',2],
					['', 'exp', Db::raw("FIND_IN_SET('{$pid}',pids)")]
				];
				$map4=[
					['types','=',1],
					['open_types','=',3],
					['', 'exp', Db::raw("FIND_IN_SET('{$uid}',uids)")]
				];			
				$course_ids = get_course($did,$pid,$uid);
				$train_ids = get_train($did,$pid,$uid);
				$whereOr =[$map1,$map2,$map3,$map4];
				if(!empty($course_ids)){
					$map5=[
						['types','=',2],
						['course_id','in',$course_ids]
					];
					$whereOr[] = $map5;
				}			
				if(!empty($train_ids)){
					$map6=[
						['types','=',3],
						['train_id','in',$train_ids]
					];
					$whereOr[] = $map6;
				}
			}
            if (!empty($param['keywords'])) {
                $where[] = ['id|title|desc', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['cate_id'])) {
                $where[] = ['cate_id', '=', $param['cate_id']];
            }
			if (!empty($param['status'])) {
                $where[] = ['status', '=', $param['status']];
            }
			else{
				 $where[] = ['status', '>', 0];
			}
			$where[]=['delete_time','=',0];
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0,'', $list);
        }
        else{
            return view();
        }
    }
	/**
    * 去考试
    */
    public function exam()
    {
		$param = get_params();
		$id = isset($param['id']) ? $param['id'] : 0;
		$exam = isset($param['e']) ? $param['e'] : 0;
		$snapshot_id = 0;
		$detail = $this->model->getById($id);
		$snapshot = Db::name('PaperSnapshot')->where(['paper_id'=>$id,'uid'=>$this->uid,'delete_time'=>0])->find();
		if(empty($snapshot)){
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
			if($exam==1){
				$snapshot = [
					'uid' => $this->uid,
					'paper_id' => $detail['id'],
					'snapshot' => serialize([]),
					'detail' => serialize($section),
					'create_time' => time()
				];
				$snapshot_id = Db::name('PaperSnapshot')->strict(false)->field(true)->insertGetId($snapshot);
			}
		}
		else{
			$snapshot_id = $snapshot['id'];
			$section = unserialize($snapshot['detail']);
		}
		View::assign('exam', $exam);
		View::assign('admin', get_admin($this->uid));
		View::assign('snapshot_id', $snapshot_id);
		View::assign('section', $section);
		View::assign('detail', $detail);
		return view();
    }

    //我的考试记录详情
    public function view()
    {
		$param = get_params();		
		$record = Db::name('PaperRecord')->where(['id'=>$param['id']])->find();
		$detail = $this->model->getById($record['paper_id']);
		$snapshot = Db::name('PaperSnapshot')->where('id',$record['snapshot_id'])->find();
		$section = unserialize($record['snapshot']);
		
		View::assign('detail', $detail);
		View::assign('section', $section);
		View::assign('record', $record);
		View::assign('snapshot', $snapshot);
		return view();
    }
}
