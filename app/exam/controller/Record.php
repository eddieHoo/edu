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

class Record extends BaseController
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
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		
			$where = [];
			if (!empty($param['keywords'])) {
                $where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
            $where[] = ['a.paper_id', '=', $param['id']];
			$where[] = ['a.delete_time', '=', 0];
			
			$list = Db::name('PaperRecord')->where($where)
				->field('a.id,a.paper_id,a.uid,a.score,a.duration,a.status,a.create_time,a.reviewer,a.reviewer_time')
				->alias('a')
				->join('PaperSnapshot s', 's.id = a.snapshot_id')
                ->order('a.id desc')
                ->paginate(['list_rows'=> $rows])
                ->each(function ($item, $key) {
					$user = Db::name('Admin')->where(['id' => $item['uid']])->find();
					$item['user_name'] = $user['name'];
					$item['department'] = Db::name('Department')->where(['id' => $user['did']])->value('title');
					$item['position'] =  Db::name('Position')->where(['id' => $user['position_id']])->value('title');
					$item['duration'] = getTimeBySec($item['duration']);
					$item['create_time'] = to_date($item['create_time'],'Y-m-d H:i');
					if($item['reviewer']>0){
						$item['reviewer_name'] = Db::name('Admin')->where(['id' => $item['reviewer']])->value('name');
						$item['reviewer_time'] = to_date($item['reviewer_time'],'Y-m-d H:i');
					}
					else{
						$item['reviewer_name'] = '-';
						$item['reviewer_time'] = '-';
					}
					return $item;
                });
            return table_assign(0, '', $list);
        } else {
			$detail = $detail = $this->model->getById($param['id']);
			$record_total = Db::name('PaperRecord')->where(['delete_time' => 0])->count();
			$record_check_0 = Db::name('PaperRecord')->where(['delete_time' => 0,'status' => 0])->count();
			$record_check_1 = Db::name('PaperRecord')->where(['delete_time' => 0,'status' => 1])->count();
			$record_check_score = Db::name('PaperRecord')->where(['delete_time' => 0,'status' => 1])->sum('score');
			$record_good = Db::name('PaperRecord')->where([['delete_time','=',0],['status','=',1],['score','>=',$detail['good_score']]])->count();
			$record_pass = Db::name('PaperRecord')->where([['delete_time','=',0],['status','=',1],['score','>=',$detail['pass_score']]])->count();
			
			if($record_total>0 && $record_check_1>0){
				$record_avg_score = round($record_check_score/$record_check_1,2);
				$record_good_pesent = round($record_good*100/$record_check_1,2);
				$record_pass_pesent = round($record_pass*100/$record_check_1,2);
			}
			else{
				$record_avg_score = '0.00';
				$record_good_pesent = '0.00';
				$record_pass_pesent = '0.00';
			}
			
			View::assign('detail', $detail);
			View::assign('record_total', $record_total);
			View::assign('record_check_0', $record_check_0);
			View::assign('record_check_1', $record_check_1);
			View::assign('record_avg_score', $record_avg_score);
			View::assign('record_good', $record_good);
			View::assign('record_good_pesent', $record_good_pesent);
			View::assign('record_pass', $record_pass);
			View::assign('record_pass_pesent', $record_pass_pesent);
            return view();
        }
    }
	
    //考试记录详情
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
	
    //考试记录审阅
    public function set()
    {
		$param = get_params();
		$record = Db::name('PaperRecord')->where(['id'=>$param['id']])->find();
		$section = unserialize($record['snapshot']);
		if (request()->isAjax()) {
			$score = $record['score'];
			$review_arr =[];
			if(!empty($param['review'])){
				$review_arr = explode('〒', $param['review']);
			}		
			$review = [];
			$answer = [];
			foreach ($review_arr as $value){
				$arr_item = explode('→', $value);
				$qid = $arr_item[0];
				$answer[] = array('question_id' => $qid, 'result' => $arr_item[1]);
			}
			if(!empty($answer)){
				foreach ($section as &$value) {
					foreach ($value['questions'] as &$val) {
						foreach ($answer as $val3) {
							if ($val3['question_id'] == $val['id'] && $val['types'] == 5) {
								$val['isok']=1;
								$val['result'] = $val3['result'];
								$score+=$val3['result'];
							}
						}
					}
				}
			}

			$record = [
				'id' => $param['id'],
				'reviewer' => $this->uid,
				'score' => $score,
				'reviewer_remark' => $param['reviewer_remark'],
				'status' => 1,
				'snapshot' => serialize($section),
				'reviewer_time' => time()
			
			];
			//dd($record);exit;
			$res = Db::name('PaperRecord')->strict(false)->field(true)->update($record);
			if ($res) {
				return to_assign(0, "批阅成功",['id'=>$param['id']]);
			} else {
				return to_assign(1, "批阅失败，请重试");
			}
		}
		else{			
			$detail = $this->model->getById($record['paper_id']);
			$snapshot = Db::name('PaperSnapshot')->where('id',$record['snapshot_id'])->find();			
			View::assign('detail', $detail);
			View::assign('section', $section);
			View::assign('record', $record);
			View::assign('snapshot', $snapshot);
			return view();
		}
    }
	
	//考试记录删除
    public function del()
    {
		$param = get_params();
		$snapshot_id = Db::name('PaperRecord')->where(['id' => $param['id']])->value('snapshot_id');
		$res = Db::name('PaperRecord')->where(['id' => $param['id']])->update(['delete_time'=>time()]);
		if($res!== false){
			Db::name('PaperSnapshot')->where(['id' => $snapshot_id])->update(['delete_time'=>time()]);
			add_log('remove', $param['id'], $param);
            to_assign();
        }
        else{
            to_assign(1,'操作失败');
        }
    }
}
