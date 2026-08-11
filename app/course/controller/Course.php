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

namespace app\course\controller;

use app\base\BaseController;
use app\course\model\Course as CourseModel;
use app\course\model\CourseRecord;
use app\course\validate\CourseCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Course extends BaseController
{	
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new CourseModel();
    }
	//列表
	public function datalist()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
		
			$where = [];
			if (!empty($param['keywords'])) {
                $where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['cate_id'])) {
                $where[] = ['cate_id', '=', $param['cate_id']];
            }
			if (!empty($param['types'])) {
                $where[] = ['types', '=', $param['types']];
            }
			$where[] = ['delete_time', '=', 0];
			$list = $this->model->datalist($param,$where);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }
	
    //新建编辑
    public function add()
    {
		$param = get_params();
        if (request()->isAjax()) {
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(CourseCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['update_time'] = time();
                $res = $this->model->update($param);
                if ($res) {
                    add_log('edit', $param['id'], $param);
                }
                return to_assign();
            } else {
                try {
                    validate(CourseCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $param['create_time'] = time();
                $param['admin_id'] = $this->uid;
                $insertId = $this->model->add($param);
                if ($insertId) {
                    add_log('add', $insertId, $param);
                }
                return to_assign();
            }
        }
		else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $types = isset($param['types']) ? $param['types'] : 0;
            if ($id > 0) {
                $detail = $this->model->getById($id);
                if (empty($detail)) {
                    return to_assign(1, '课程不存在');
                }
				View::assign('detail', $detail);
				return view('edit');
            }
			View::assign('types', $types);
			return view();
        }
    }
	
    //查看
    public function view()
    {
        $param = get_params();
        $id = isset($param['id']) ? $param['id'] : 0;
        $detail = $this->model->getById($id);
        if (empty($detail)) {
            return to_assign(1, '课程不存在');
        } else {
			$section = Db::name('CourseSection')->where(['course_id'=>$detail['id']])->select()->toArray();
			foreach ($section as $key => &$value) {
				$value['material'] = [];
				if(!empty($value['mids'])){
					$ids = $value['mids'];
					$exp = ("field(id,".$ids.")");
					$material = Db::name('Material')->where([['id','in',$ids]])->orderRaw($exp)->select()->toArray();
					foreach ($material as $key => &$val) {
						$val['type_str'] = content_types_name($val['types']);
					}
					$value['material'] = $material;
				}
			}
            View::assign('section', $section);
            View::assign('detail', $detail);
            return view();
        }
    }
	
	
	//章节
    public function section()
    {
        $param = get_params();
        if (request()->isAjax()) {
			//相关内容多个数组;
			$title_data = isset($param['title']) ? $param['title'] : '';
			$mids_data = isset($param['mids']) ? $param['mids'] : '';
			foreach ($mids_data as $key => $value) {
				if ($value == '') {
					return to_assign(1,'第' . ($key + 1) . '章节的内容未完善');
				}
			}						
			$course_id = $param['id'];	
			Db::name('CourseSection')->where(['course_id'=>$course_id])->delete();					
			if ($title_data) {
				foreach ($title_data as $key => $value) {
					if (!$value) {
						continue;
					}
					$data = [];
					$data['title'] = $value;
					$data['mids'] = $mids_data[$key];
					$data['course_id'] = $course_id;
					$data['admin_id'] = $this->uid;
					$data['create_time'] = time();
					Db::name('CourseSection')->strict(false)->field(true)->insertGetId($data);
				}
			}
			$mids = Db::name('CourseSection')->where(['course_id'=>$course_id])->column('mids');
			$midsStr = implode(',',$mids);
			$duration = Db::name('Material')->where([['id','in',$midsStr]])->sum('duration');
			$credit = Db::name('Material')->where([['id','in',$midsStr]])->sum('integral');
			$res =  $this->model::strict(false)->where('id',$course_id)->update(['duration' => $duration,'credit' => $credit,'update_time' => time()]);
			if ($res) {
				return to_assign();	
			}
			else{
				return to_assign(0, '操作失败');	
			}					
		}
		else {
			$detail = $this->model->getById($param['id']);
			$section = Db::name('CourseSection')->where(['course_id'=>$detail['id']])->select()->toArray();
			foreach ($section as $key => &$value) {
				$value['material'] = [];
				if(!empty($value['mids'])){
					$ids = $value['mids'];
					$exp = ("field(id,".$ids.")");
					$material = Db::name('Material')->where([['id','in',$ids]])->orderRaw($exp)->select()->toArray();
					foreach ($material as $key => &$val) {
						$val['type_str'] = content_types_name($val['types']);
					}
					$value['material'] = $material;
				}
			}
            View::assign('section', $section);
            View::assign('detail', $detail);
            return view();
        }
    }
	
	//设置
    public function check()
    {
		$param = get_params();
        $res = Db::name('Course')->strict(false)->field('id,status')->update($param);
		if ($res) {
			if($param['status'] == 0){
				add_log('putoff', $param['id'], $param);
			}
			else if($param['status'] == 1){
				add_log('release', $param['id'], $param);
			}
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
		}
    } 
	
    //删除
    public function del()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            $count_course = Db::name('CourseRecord')->where([['course_id', '=', $id], ['delete_time', '=', 0]])->count();
            if ($count_course > 0) {
                return to_assign(1, "该课程有学习记录，无法删除");
            }
            if (Db::name('Course')->where('id', $id)->update(['delete_time' => time()]) !== false) {
                return to_assign(0, "删除成功");
            } else {
                return to_assign(0, "删除失败");
            }
        } else {
            return to_assign(1, "错误的请求");
        }
    }
	
	//学习记录
	public function record()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];		
			$where = [];
			if (!empty($param['keywords'])) {
                $where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
            $where[] = ['course_id', '=', $param['id']];
			$where[] = ['delete_time', '=', 0];
			
			$model = new CourseRecord();
			$list = $model->datalist($where, $param);
            return table_assign(0, '', $list);
        } else {
			$detail = $this->model->getById($param['id']);
			View::assign('detail', $detail);
            return view();
        }
    }
   
	
	//删除课程学习记录
    public function record_del()
    {
		$param = get_params();
		$detail = Db::name('CourseRecord')->where(['id' => $param['id']])->find();
		$res = Db::name('CourseRecord')->where(['id' => $param['id']])->update(['delete_time'=>time()]);
		if($res!== false){
			Db::name('MaterialRecord')->where(['course_id' => $detail['course_id'],'uid'=>$detail['uid']])->update(['delete_time'=>time()]);
			add_log('remove', $param['id'], $param);
            to_assign();
        }
        else{
            to_assign(1,'操作失败');
        }
    }
}
