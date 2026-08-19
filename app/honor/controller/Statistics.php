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

namespace app\honor\controller;

use app\base\BaseController;
use app\exam\model\Paper;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Statistics extends BaseController
{	

	// 统计培训数量	
    function total($uid=0,$did=0,$pid=0) {
		//线上课程
		$course_a= Db::name('CourseRecord')->where([['uid','=',$uid],['delete_time','=',0],['progress','=',0]])->count();
		$course_b = Db::name('CourseRecord')->where([['uid','=',$uid],['delete_time','=',0],['progress','<',100]])->count();
		$course_b = $course_b - $course_a;
		$course_c = Db::name('CourseRecord')->where([['uid','=',$uid],['delete_time','=',0],['progress','=',100]])->count();
		
		//面授培训
		$where=[];
		$whereOr=[];
		$where[]=['delete_time','=',0];
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
		//培训总数
		$train_a = Db::name('Train')
			->where(function ($query) use ($whereOr) {
                if (!empty($whereOr)){
                    $query->whereOr($whereOr);
				}
            })
			->where($where)->where([['status','=',1]])->count();
		$train_b = Db::name('Train')
			->where(function ($query) use ($whereOr) {
                if (!empty($whereOr)){
                    $query->whereOr($whereOr);
				}
            })
			->where($where)->where([['status','=',2]])->count();
		$train_c = Db::name('Train')
			->where(function ($query) use ($whereOr) {
                if (!empty($whereOr)){
                    $query->whereOr($whereOr);
				}
            })
			->where($where)->where([['status','=',3]])->count();
		
		//在线考试
		$paper_where=[];
		$paper_whereOr=[];
		$paper_map1=[
			['types','=',1],
			['open_types','=',0]
		];
		$paper_map2=[
			['types','=',1],
			['open_types','=',1],
			['', 'exp', Db::raw("FIND_IN_SET('{$did}',dids)")]
		];
		$paper_map3=[
			['types','=',1],
			['open_types','=',2],
			['', 'exp', Db::raw("FIND_IN_SET('{$pid}',pids)")]
		];
		$paper_map4=[
			['types','=',1],
			['open_types','=',3],
			['', 'exp', Db::raw("FIND_IN_SET('{$uid}',uids)")]
		];
		$paper_whereOr =[$paper_map1,$paper_map2,$paper_map3,$paper_map4];
		$course_ids = get_course($did,$pid,$uid);
		$train_ids = get_train($did,$pid,$uid);				
		if(!empty($course_ids)){
			 $paper_map5=[
				['types','=',2],
				['course_id','in',$course_ids]
			];
			$paper_whereOr[] = $paper_map5;
		}			
		if(!empty($train_ids)){
			$paper_map6=[
				['types','=',3],
				['train_id','in',$train_ids]
			];
			$paper_whereOr[] = $paper_map6;
		}
		
		$paper_where[] = ['status', '>', 0];
		$paper_where[]=['delete_time','=',0];
				
		//在线考试总数
        $paper_count = Db::name('Paper')
			->where(function ($query) use ($paper_whereOr) {
                if (!empty($paper_whereOr)){
                    $query->whereOr($paper_whereOr);
				}
            })
			->where($paper_where)->count();
			
		//考试完成数	
        $paper_b = Db::name('PaperRecord')->where(['uid'=>$uid,'delete_time'=>0])->group('paper_id')->count();	
			
		$paper_a = $paper_count-$paper_b;
		if($paper_b<0){
			$paper_b = 0;
		}
		
		$res = [
			'course_a'=>$course_a,
			'course_b'=>$course_b,
			'course_c'=>$course_c,
			'train_a'=>$train_a,
			'train_b'=>$train_b,
			'train_c'=>$train_c,
			'paper_a'=>$paper_a,
			'paper_b'=>$paper_b
		];
        return $res;
    }
	
	public function index()
    {
        if (request()->isAjax()) {
            $param = get_params();
			$where = [];
			if (!empty($param['keywords'])) {
                $where[] = ['a.name', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['dids'])) {
                $where[] = ['a.did', 'in', $param['dids']];
            }
			if (!empty($param['pids'])) {
                $where[] = ['a.position_id', 'in', $param['pids']];
            }
			$where[] = ['a.status', '=', 1];
            $list = Db::name('Admin')
				->field('a.id,a.name,a.thumb,a.did,a.position_id,a.type,a.integral,a.credit,d.title as department,p.title as position')
				->alias('a')
				->join('department d','d.id = a.did')
				->join('position p','p.id = a.position_id')
				->where($where)
				->order('a.id desc')
				->select()
				->toArray();
			foreach ($list as $key => &$value) {
				$total = $this->total($value['id'],$value['did'],$value['position_id']);
				
				$value['course_a'] = $total['course_a'];
				$value['course_b'] = $total['course_b'];
				$value['course_c'] = $total['course_c'];
				
				$value['train_a'] = $total['train_a'];
				$value['train_b'] = $total['train_b'];
				$value['train_c'] = $total['train_c'];
				
				$value['paper_a'] = $total['paper_a'];
				$value['paper_b'] = $total['paper_b'];
				
				$value['certificate'] = Db::name('CertificateUsers')->where('user_id', $value['id'])->count();
			}		
			$res['data'] = $list;
            return table_assign(0, '', $res);
        } else {
            return view();
        }
    }
    //学员排行榜
    public function ranking()
    {
		$rank_credit = Db::name('Admin')
				->field('a.id,a.name,a.thumb,a.did,a.position_id,a.type,a.integral,a.credit,d.title as department,p.title as position')
				->alias('a')
				->join('department d','d.id = a.did')
				->join('position p','p.id = a.position_id')
				->where(['a.status'=>1])
				->order('a.credit desc')
				->select()
				->toArray();
				
		$rank_integral = Db::name('Admin')
				->field('a.id,a.name,a.thumb,a.did,a.position_id,a.type,a.integral,a.credit,d.title as department,p.title as position')
				->alias('a')
				->join('department d','d.id = a.did')
				->join('position p','p.id = a.position_id')
				->where(['a.status'=>1])
				->order('a.integral desc')
				->select()
				->toArray();

		View::assign('rank_credit', $rank_credit);
		View::assign('rank_integral', $rank_integral);
		return view();
    }  
}
