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
use app\course\model\Lecturer as LecturerModel;
use app\course\model\Course as CourseList;
use app\train\model\Train as TrainList;
use app\course\validate\LecturerCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Lecturer extends BaseController
{	
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new LecturerModel();
    }
	
    /**
    * 数据列表
    */
    public function datalist()
    {
        if (request()->isAjax()) {
            $param = get_params();
			$where = [];
			if (!empty($param['keywords'])) {
                $where[] = ['name|mobile', 'like', '%' . $param['keywords'] . '%'];
            }
			if (isset($param['status']) && $param['status']!='') {
				$where[] = ['status', '=', $param['status']];
            }
			else{
               $where[] = ['status','>=',0];
			}
			if (!empty($param['level'])) {
                $where[] = ['level', '=', $param['level']];
            }
			if (!empty($param['education'])) {
                $where[] = ['education', '=', $param['education']];
            }
			$where[] = ['delete_time', '=', 0];
			$list = $this->model->datalist($where, $param);
            return table_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //添加
    public function add()
    {
        $param = get_params();
        if (request()->isPost()) {
			if (!empty($param['id']) && $param['id'] > 0) {
				$data['update_time'] = time();
                $res = $this->model->edit($param);
                if ($res!==false) {
                    add_log('edit', $param['id'], $param);
					return to_assign();
                }
				else{
					return to_assign(1,'操作失败');
				}
			}
			else{
				$param['create_time'] = time();
				$param['admin_id'] = $this->uid;
				$sid = $this->model->add($param);
				if ($sid!==false) {		
					add_log('add', $sid, $param);
					return to_assign();
				}else{
					return to_assign(1,'操作失败');
				}
			}			
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $detail = $this->model->getById($id);
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
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
            return to_assign(1, '讲师不存在');
        } else {
			$detail['count_course'] = Db::name('Course')->where([['lecturer_id', '=', $id], ['delete_time', '=', 0]])->count();
			$map = [];
			$map[] = ['delete_time', '=', 0];
			$map[] = ['', 'exp', Db::raw("FIND_IN_SET('{$id}',lecturer_ids)")];
			$detail['count_train'] = Db::name('Train')->where($map)->count();
			
			$course_ids = Db::name('Course')->where([['lecturer_id', '=', $id], ['delete_time', '=', 0]])->column('id');
			$uids_array = Db::name('CourseRecord')->where([['course_id', 'in', $course_ids], ['delete_time', '=', 0]])->column('uid');
			$uids = implode(',',$uids_array);
			$uids_array = explode(',',$uids);
			//去除相同的学员
			$uids_array = array_unique($uids_array);
			$student_array= array_filter($uids_array);			
			$detail['count_student'] = count($student_array);
			
			$course = CourseList::withoutField('desc,content')
                ->where([['lecturer_id', '=', $id],['delete_time', '=', 0]])
                ->order('id desc')
                ->select()
                ->each(function ($item, $key) {
                    $item->cate = Db::name('CourseCate')->where(['id' => $item->cate_id])->value('title');
					$item->typestr = get_content_type($item->type);
					$item->duration = getTimeBySec($item->duration);
                });
				
			$train = TrainList::withoutField('desc,content')
                ->where([['lecturer_ids', '=', $id],['delete_time', '=', 0]])
                ->order('id desc')
                ->select()
                ->each(function ($item, $key) {
                    $item->cate = Db::name('TrainCate')->where(['id' => $item->cate_id])->value('title');
					//$item->lecturer = Db::name('Lecturer')->where(['id' => $item->lecturer_id])->value('name');
                    $item->open_time = to_date($item->open_time,'Y-m-d H:i');
                });	
            View::assign('detail', $detail);
            View::assign('course', $course);
            View::assign('train', $train);
            return view();
        }
    }
	
	//设置
    public function check()
    {
		$param = get_params();
        $res = Db::name('Lecturer')->strict(false)->field('id,status')->update($param);
		if ($res) {
			if($param['status'] == 0){
				add_log('disable', $param['id'], $param);
			}
			else if($param['status'] == 1){
				add_log('recovery', $param['id'], $param);
			}
			return to_assign();
		}
		else{
			return to_assign(0, '操作失败');
		}
    } 

   /**
    * 删除
    */
    public function del()
    {
		$param = get_params();
		$id = isset($param['id']) ? $param['id'] : 0;
		if (request()->isDelete()) {
			$this->model->delById($id);
		} else {
            return to_assign(1, "错误的请求");
        }
    } 
}
