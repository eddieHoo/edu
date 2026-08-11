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

namespace app\train\controller;

use app\base\BaseController;
use app\train\model\Train as TrainModel;
use app\train\model\TrainCate as TrainCateModel;
use app\train\validate\TrainValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Train extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new TrainModel();
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
                $where[] = ['id|title|desc', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['cate_id'])) {
                $where[] = ['cate_id', '=', $param['cate_id']];
            }
			if (!empty($param['status'])) {
                $where[] = ['status', '=', $param['status']];
            }
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
            return view();
        }
    }
	
	/**
    * 培训评价列表
    */
    public function appraise()
    {
		$param = get_params();
		$uid = $this->uid;
        if (request()->isAjax()) {
			$train = Db::name('Train')->where(['id'=>$param['id']])->find();
			$where = [];
			if($train['types'] == 0){
				$where[] = ['a.id','>',1];
			}
			if($train['types'] == 1){
				$where[] = ['a.did','in',$train['dids']];
			}
			if($train['types'] == 2){
				$where[] = ['a.position_id','in',$train['pids']];
			}
			if($train['types'] == 3){
				$where[] = ['a.id','in',$train['uids']];
			}
			$list = Db::name('Admin')
                ->field('a.id as uid,a.name,a.did,a.position_id,d.title as department,p.title as position')
                ->alias('a')
                ->join('Position p', 'p.id = a.position_id', 'left')
                ->join('Department d', 'd.id = a.did', 'left')
                ->where($where)
                ->paginate(['list_rows'=> 9999])
                ->each(function ($item, $key) use($train){
                    $appraise = Db::name('TrainRecord')->where(['train_id'=>$train['id'],'uid'=>$item['uid']])->find();
					if(!empty($appraise)){
						 $item['appraise_time'] = date('Y-m-d H:i',$appraise['appraise_time']);
						 $item['appraise_admin'] = $appraise['appraise_admin'];
						 $item['appraise_admin_score'] = $appraise['appraise_admin_score'];
						 $item['appraise_user'] = $appraise['appraise_user'];
						 $item['appraise_user_score'] = $appraise['appraise_user_score'];
						 $item['appraise_admin_id'] = $appraise['appraise_admin_id'];
					}
					else{
						 $item['appraise_time'] = 0;
						 $item['appraise_admin'] = '-';
						 $item['appraise_admin_score'] = 0;
						 $item['appraise_user'] = '-';
						 $item['appraise_user_score'] = 0;
						 $item['appraise_admin_id'] = 0;
					}
                    return $item;
                });
            return table_assign(0, '', $list);
        }
        else{
			$detail = $this->model->getById($param['id']);
			$appraise = Db::name('TrainRecord')->where(['train_id'=>$detail['id'],'uid'=>$uid])->find();
			View::assign('detail',$detail);
			View::assign('appraise',$appraise);
            return view();
        }
    }
	
	/*
     *评价详情
     */
    public function appraise_view()
    {
		$param = get_params();
		$detail = Db::name('Train')->where(['id'=>$param['train_id']])->find();
		$appraise = Db::name('TrainRecord')->where(['train_id'=>$detail['id'],'uid'=>$param['uid']])->find();
		$admin = get_admin($param['uid']);
		View::assign('detail',$detail);
		View::assign('appraise',$appraise);
		View::assign('admin',$admin);
		return view();
	}
	
	/*
     *评价学员
     */
    public function appraise_add()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$appraise = Db::name('TrainRecord')->where(['train_id'=>$param['train_id'],'uid'=>$param['uid']])->find();
			if(empty($appraise)){
				 $res = Db::name('TrainRecord')->insert(['uid'=>$param['uid'],'train_id'=>$param['train_id'],'appraise_user'=>$param['appraise_user'],'appraise_user_score'=>$param['appraise_user_score'],'appraise_time'=>time()]);		
			}
			else{
				$res =  Db::name('TrainRecord')->update(['id'=>$appraise['id'],'appraise_user'=>$param['appraise_user'],'appraise_user_score'=>$param['appraise_user_score']]);
			}			
			if($res){
				to_assign();
			}
			else{
				to_assign(1,'操作失败');
			}
        }
        else{
			$detail = Db::name('Train')->where(['id'=>$param['train_id']])->find();
			$appraise = Db::name('TrainRecord')->where(['train_id'=>$detail['id'],'uid'=>$param['uid']])->find();
			$admin = get_admin($param['uid']);
			View::assign('detail',$detail);
			View::assign('appraise',$appraise);
			View::assign('admin',$admin);
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
			$param['content'] = serialize([]);		
			$plan_title_data = isset($param['plan_title']) ? $param['plan_title'] : '';
			$plan_time_data = isset($param['plan_time']) ? $param['plan_time'] : '';
			$plan_content_data = isset($param['plan_content']) ? $param['plan_content'] : '';
			$plan_lecturer_data = isset($param['plan_lecturer']) ? $param['plan_lecturer'] : '';
			$plan_lecturer_id_data = isset($param['plan_lecturer_id']) ? $param['plan_lecturer_id'] : 0;
			$plan_types_data = isset($param['plan_types']) ? $param['plan_types'] : '';				
			$plan_result_data = isset($param['plan_result']) ? $param['plan_result'] : '';				
			$plan = [];
			if(!empty($plan_title_data)){
				foreach ($plan_title_data as $key => $value) {
					if (!$value) {
						continue;
					}
					$data = [];
					$data['plan_title'] = $plan_title_data[$key];
					$data['plan_time'] = $plan_time_data[$key];
					$data['plan_content'] = $plan_content_data[$key];
					$data['plan_lecturer'] = $plan_lecturer_data[$key];
					$data['plan_lecturer_id'] = $plan_lecturer_id_data[$key];
					$data['plan_types'] = $plan_types_data[$key];
					$data['plan_result'] = $plan_result_data[$key];
					$plan[]=$data;
				}
			}
			$param['content'] = serialize($plan);
			
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(IndexValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(IndexValidate::class)->scene('add')->check($param);
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
		if (!empty($detail)) {
			$detail['cate'] =  Db::name('TrainCate')->where('id',$detail['cate_id'])->value('title');
			$detail['paper'] =  Db::name('Paper')->where(['train_id'=>$detail['id'],'delete_time'=>0])->select()->toArray();
			View::assign('detail', $detail);
			return view();
		}
		else{
			return view(EEEOR_REPORTING,['code'=>404,'warning'=>'找不到页面']);
		}
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
}
