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

use app\api\BaseController;
use app\exam\model\Paper;
use app\exam\model\Question;
use think\facade\Db;
use think\facade\View;

class Api extends BaseController
{
	//获取试卷
	public function get_paper()
    {
		$param = get_params();
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
		$where[] = ['status', '>', 0];
		$where[]=['delete_time','=',0];
		$model = new Paper();
		$list = $model->datalist($param,$where,$whereOr);
		return table_assign(0,'', $list);
    }
	
	//获取试题
	public function get_question()
    {
        $param = get_params();
		$where = array();
		if (!empty($param['keywords'])) {
			$where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
		}
		if (!empty($param['cate_id'])) {
			$where[] = ['cate_id', '=', $param['cate_id']];
		}
		if (!empty($param['types'])) {
			$where[] = ['types', '=', $param['types']];
		}
		if (!empty($param['level'])) {
			$where[] = ['level', '=', $param['level']];
		}
		if (!empty($param['qids'])) {
			$where[] = ['id', 'notin', $param['qids']];
		}
		if (!empty($param['ids'])) {
			$where[] = ['id', 'in', $param['ids']];
			$rows='99999';
		}
		$where[] = ['delete_time', '=', 0];
		$where[] = ['status', '=', 1];		
		$model = new Question();
        $list = $model->datalist($param,$where);
        return table_assign(0, '', $list);
    }	
	
	/*
     *保存考试答案
     */
    public function save_answer()
    {
        $param = get_params();
        $uid = $this->uid;
		$exam_times = Db::name('Paper')->where(['id' => $param['paper_id']])->value('exam_times');
		if($exam_times>0){
			$has = Db::name('PaperRecord')->where(['uid' => $uid, 'paper_id' => $param['paper_id'], 'delete_time' => '0'])->count();
			if ($has>=$exam_times) {
				return to_assign(1, "您已经参加过".$has."次考试，不能再参与！");
			}
		}
		//读取试卷快照
		$paper_snapshot = Db::name('PaperSnapshot')->where('id',$param['snapshot_id'])->value('detail');
		$section = unserialize($paper_snapshot);
		$answer_arr =[];
		if(!empty($param['answer'])){
			$answer_arr = explode('〒', $param['answer']);
		}		
		$answer = [];
		foreach ($answer_arr as $value){
			$arr_item = explode('→', $value);
			$qid = $arr_item[0];
			$answerinfo = $arr_item[1];
			$answer[] = array('uid' => $uid, 'question_id' => $qid, 'content' => $answerinfo, 'paper_id' => $param['paper_id']);
		}		
		
		$score=0;
		$integral=0;
        foreach ($section as &$value) {
			foreach ($value['questions'] as &$val) {
				$val['answers'] = '';
				$val['isok']=0;//未作答+错误
				$val['result']=0;//得分
				foreach ($answer as $val3) {
					if ($val3['question_id'] == $val['id']) {
						$val['answers'] = $val3['content'];
						//判断+单选+多选+填空题
						if ($val['types'] == 1 || $val['types'] == 2 || $val['types'] == 3 ||  $val['types'] == 4) {
							if ($val['questionanswer'] == $val3['content']) {
								$val['isok']=1;//正确
								$val['result'] = $val['score'];
								$score+=$val['score'];
								$integral+=$val['integral'];
							}
						}
						if($val['types'] == 5 && $val3['content'] != ''){
							$val['isok']=2;
						}
					}
				}
			}
        }
		
		$record = [
			'uid' => $uid,
			'score' => $score,
			'integral' => 0,
			'reviewer_remark' => '',
			'snapshot_id' => $param['snapshot_id'],
			'paper_id' => $param['paper_id'],
			'duration' => $param['duration'],
			'snapshot' => serialize($section),
			'answer' => serialize($answer),
			'create_time' => time()
		
		];
		$insertId = Db::name('PaperRecord')->strict(false)->field(true)->insertGetId($record);
		if ($insertId) {
			return to_assign(0, "提交成功",['id'=>$insertId]);
		} else {
			return to_assign(1, "提交失败，请重试");
		}
    }
}
