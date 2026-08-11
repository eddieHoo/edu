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
class Lecturer extends Model
{
    /**
    * 获取分页列表
    * @param $where
    * @param $param
    */
    public function datalist($where, $param)
    {
		$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		$order = empty($param['order']) ? 'id desc' : $param['order'];
        try {
            $list = self::where($where)
			->withoutField('content')
			->order($order)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key){
				$item->education_str = education_name($item->education);
				$item->level_str = level_name($item->level);
				$item->count_course = Db::name('Course')->where([['lecturer_id', '=', $item->id], ['delete_time', '=', 0]])->count();
				
				$id = $item->id;
				$item->count_train = Db::name('Train')->where([['delete_time', '=', 0],['', 'exp', Db::raw("FIND_IN_SET('{$id}',lecturer_ids)")]])->count();
				
				$course_ids = Db::name('Course')->where([['lecturer_id', '=', $item->id], ['delete_time', '=', 0]])->column('id');
				$count_student = Db::name('CourseRecord')->where([['course_id', 'in', $course_ids], ['delete_time', '=', 0]])->column('uid');
				$uids_array = array_unique($count_student);		
				$item->count_student = count($uids_array);
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
		$info['admin_name'] = Db::name('Admin')->where(['id' => $info['admin_id']])->value('name');
		if($info['thumb']>0){
			$info['thumb_str'] = get_file($info['thumb']);
		}
		else{
			$info['thumb_str']='/static/home/images/lecturer.jpg';
		}
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
