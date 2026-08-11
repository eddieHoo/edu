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

namespace app\exam\model;
use think\model;
use think\facade\Db;
class Paper extends Model
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
			->order($order)
			->paginate(['list_rows'=> $rows])
			->each(function ($item, $key) use($param){
				$item['cate'] = Db::name('PaperCate')->where('id',$item['cate_id'])->value('title');
				$item['admin_name'] = Db::name('Admin')->where('id',$item['admin_id'])->value('name');
				$item['train']='-';
				if($item['train_id']>0){
					$item['train'] = Db::name('Train')->where('id',$item['train_id'])->value('title');
				}
				if($item['course_id']>0){
					$item['train'] = Db::name('Course')->where('id',$item['course_id'])->value('title');
				}
				$item['start_time'] = to_date($item['start_time'],'Y-m-d H:m');
				$item['end_time'] = to_date($item['end_time'],'Y-m-d H:m');
				$item['hour'] = intval($item['duration']/3600);
				$item['minute'] = intval(($item['duration']%3600)/60);
				$item['record_count']=0;
				if(!empty($param['admin_id'])){
					$item['record_count'] = Db::name('PaperRecord')->where(['delete_time' => 0,'paper_id' =>$item['id'],'uid'=>$param['admin_id']])->count();
				}
				$item['create_time'] = to_date($item['create_time']);
				$item['section'] = Db::name('PaperSection')->where('paper_id',$item['id'])->count();
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
		$info['cate'] = Db::name('PaperCate')->where(['id' => $info['cate_id']])->value('title');
		$info['start_time_str'] = to_date($info['start_time'],'Y-m-d H:m');
		$info['end_time_str'] = to_date($info['end_time'],'Y-m-d H:m');
		$info['hour'] = intval($info['duration']/3600);
		$info['minute'] = intval(($info['duration']%3600)/60);
		$info['departments'] = '';
		$info['positions'] = '';
		$info['unames'] = '';
		
		if($info['types'] == 1 && $info['open_types'] == 1){
			$departments = Db::name('Department')->where([['id','in', $info['dids']]])->column('title');
			$info['departments'] = implode(',',$departments);
		}		
		if($info['types'] == 1 && $info['open_types'] == 2){
			$positions = Db::name('Position')->where([['id','in', $info['pids']]])->column('title');
			$info['positions'] = implode(',',$positions);
		}
		if($info['types'] == 1 && $info['open_types'] == 3){
			$unames = Db::name('Admin')->where([['id','in', $info['uids']]])->column('name');
			$info['unames'] = implode(',',$unames);
		}
		
		if($info['types'] == 2){
			$course = Db::name('Course')->where('id',$info['course_id'])->find();
			$info['course'] = $course['title'];
		}		
		if($info['types'] == 3){
			$train = Db::name('Train')->where('id',$info['train_id'])->find();
			$info['train'] = $train['title'];
		}
		$reviewer_names = Db::name('Admin')->where([['id','in', $info['reviewer']]])->column('name');
		$info['reviewer_names'] = implode(',',$reviewer_names);
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

