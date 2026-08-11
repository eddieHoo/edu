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
use app\train\validate\TrainValidate;
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
        $this->model = new TrainModel();
    }
	
	/**
    * 我的培训列表
    */
    public function datalist()
    {
		$param = get_params();
        if (request()->isAjax()) {
			$uid = $this->uid;
			$did = $this->did;
			$pid = $this->poid;
			$where=[];
			$whereOr=[];
			if($uid>1){
				$map1=[
					['types','=',0]
				];
				$map2=[
					['types','=',1],
					['', 'exp', Db::raw("FIND_IN_SET('{$did}',dids)")]
				];
				$map3=[
					['types','=',2],
					['', 'exp', Db::raw("FIND_IN_SET('{$pid}',pids)")]
				];
				$map4=[
					['types','=',3],
					['', 'exp', Db::raw("FIND_IN_SET('{$uid}',uids)")]
				];
				$whereOr =[$map1,$map2,$map3,$map4];
			}
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
			else{
				 $where[] = ['status', '>', 1];
			}
			
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
            return view();
        }
    }
	
	/**
    * 我的培训评价详情
    */
    public function appraise_view()
    {
		$param = get_params();
		$uid = $this->uid;
		$detail = $this->model->getById($param['id']);
		$appraise = Db::name('TrainRecord')->where(['train_id'=>$detail['id'],'uid'=>$uid])->find();
		View::assign('detail',$detail);
		View::assign('appraise',$appraise);
		return view();
	}
	
	/*
     *评价讲师
     */
    public function appraise_add()
    {
        $param = get_params();
        $uid = $this->uid;
        if (request()->isAjax()) {
            $appraise = Db::name('TrainRecord')->where(['train_id'=>$param['train_id'],'uid'=>$uid])->find();
            if(empty($appraise)){
                 $res = Db::name('TrainRecord')->insert(['uid'=>$uid,'train_id'=>$param['train_id'],'appraise_admin'=>$param['appraise_admin'],'appraise_admin_score'=>$param['appraise_admin_score'],'appraise_time'=>time()]);        
            }
            else{
                $res =  Db::name('TrainRecord')->update(['id'=>$appraise['id'],'appraise_admin'=>$param['appraise_admin'],'appraise_admin_score'=>$param['appraise_admin_score']]);
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
            $appraise = Db::name('TrainRecord')->where(['train_id'=>$detail['id'],'uid'=>$uid])->find();
            View::assign('detail',$detail);
            View::assign('appraise',$appraise);
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
}
