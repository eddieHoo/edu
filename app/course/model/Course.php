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

namespace app\course\model;
use think\facade\Db;
use think\Model;

class Course extends Model
{
    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function datalist($param=[],$where=[],$whereOr=[])
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        try {
            $list = self::where($where)
			->where(function ($query) use($whereOr) {
				if (!empty($whereOr)){
					$query->whereOr($whereOr);
				}
			})
			->withoutField('content')
			->order($order)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key) use ($param){
				$item['admin_name'] = Db::name('Admin')->where(['id' => $item['admin_id']])->value('name');
				$item['catestr'] =  Db::name('CourseCate')->where(['id' => $item['cate_id']])->value('title');
				$item['lecturer'] =  Db::name('Lecturer')->where(['id' => $item['lecturer_id']])->value('name');
				$item['typestr'] = content_types_name($item['types']);
				$item['types_learn'] = '全体成员';
				if($item['learn_types']==1){
					$departments = Db::name('Department')->where([['id','in',$item['dids']]])->column('title');
					$item['types_learn'] = implode(',' ,$departments);
				}
				if($item['learn_types']==2){
					$positions = Db::name('Position')->where([['id','in',$item['pids']]])->column('title');
					$item['types_learn'] = implode(',' ,$positions);
				}
				if($item['learn_types']==3){
					$unames = Db::name('Admin')->where([['id','in',$item['uids']]])->column('name');
					$item['types_learn'] = implode(',' ,$unames);
				}
				$item['hour'] = intval($item['duration']/3600);
				$item['minute'] = intval(($item['duration']%3600)/60);
				$item['create_time'] = to_date($item['create_time']);
				$item['progress'] = 0;
				if(!empty($param['uid'])){
					$record = Db::name('CourseRecord')->where(['uid'=>$param['uid'],'course_id'=>$item['id'],'delete_time'=>0])->find();
					if(!empty($record)){
						$item['progress'] = $record['progress'];
					}
				}
			});
			return $list;
        } catch(\Exception $e) {
            return ['code' => 1, 'data' => [], 'msg' => $e->getMessage()];
        }
    }
	
    /**
    * 添加数据
    * @param $param
    */
    public function add($param)
    {
		$insertId = 0;
        try {
			$param['create_time'] = time();
			$insertId = self::strict(false)->field(true)->insertGetId($param);
			add_log('add', $insertId, $param);
        } catch(\Exception $e) {
			return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
		return to_assign(0,'操作成功',['return_id'=>$insertId]);
    }

    /**
    * 编辑信息
    * @param $param
    */
    public function edit($param)
    {
        try {
            $param['update_time'] = time();
            self::where('id', $param['id'])->strict(false)->field(true)->update($param);
			add_log('edit', $param['id'], $param);
        } catch(\Exception $e) {
			return to_assign(1, '操作失败，原因：'.$e->getMessage());
        }
		return to_assign(0,'操作成功',['return_id'=>$param['id']]);
    }
	
    /**
    * 根据id获取信息
    * @param $id
    */
    public function getById($id)
    {
        $info = self::find($id);
		$info['lecturer'] = Db::name('Lecturer')->where(['id' => $info['lecturer_id']])->value('name');
		$info['cate'] = Db::name('CourseCate')->where(['id' => $info['cate_id']])->value('title');
		$info['hour'] = intval($info['duration']/3600);
		$info['minute'] = intval(($info['duration']%3600)/60);
		$info['typestr'] = content_types_name($info['types']);
		$info['types_learn'] = '全体成员';
		if($info['learn_types']==1){
			$departments = Db::name('Department')->where([['id','in',$info['dids']]])->column('title');
			$info['types_learn'] = implode(',' ,$departments);
		}
		if($info['learn_types']==2){
			$positions = Db::name('Position')->where([['id','in',$info['pids']]])->column('title');
			$info['types_learn'] = implode(',' ,$positions);
		}
		if($info['learn_types']==3){
			$unames = Db::name('Admin')->where([['id','in',$info['uids']]])->column('name');
			$info['types_learn'] = implode(',' ,$unames);
		}
		if($info['courseware'] !=''){
			$file_array = Db::name('File')->where('id','in',$info['courseware'])->select();
			$info['file_array'] = $file_array;
		}
		$paper_array = Db::name('Paper')->where(['course_id'=>$info['id'],'types'=>2,'delete_time'=>0,'status'=>1])->select()->toArray();
		$info['paper_array'] = $paper_array;
		return $info;
    }

    /**
    * 删除信息
    * @param $id
    * @param $type
    * @return array
    */
    public function delById($id,$type=0)
    {
		if($type==0){
			//逻辑删除
			try {
				$param['delete_time'] = time();
				self::where('id', $id)->update(['delete_time'=>time()]);
				add_log('delete', $id);
			} catch(\Exception $e) {
				return to_assign(1, '操作失败，原因：'.$e->getMessage());
			}
		}
		else{
			//物理删除
			try {
				self::destroy($id);
				add_log('delete', $id);
			} catch(\Exception $e) {
				return to_assign(1, '操作失败，原因：'.$e->getMessage());
			}
		}
		return to_assign();
    }
}
