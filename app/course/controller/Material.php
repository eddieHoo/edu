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
use app\course\model\Material as MaterialModel;
use app\course\validate\MaterialCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Material extends BaseController
{	
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new MaterialModel();
    }
	public function datalist()
    {
        if (request()->isAjax()) {
            $param = get_params();
			$where = [];
			if (!empty($param['keywords'])) {
                $where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['types'])) {
                $where[] = ['types', '=', $param['types']];
            }
			if (!empty($param['cate_id'])) {
                $where[] = ['cate_id', '=', $param['cate_id']];
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
				try {
                    validate(MaterialCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
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
				try {
                    validate(MaterialCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
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
            $types = isset($param['types']) ? $param['types'] : 0;
            if ($id > 0) {
                $detail = $this->model->getById($id);
				$types = $detail['types'];
                View::assign('detail', $detail);
            }
            View::assign('id', $id);
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
		$host = "//".$_SERVER ['HTTP_HOST'];
		View::assign('host', $host);
		View::assign('detail', $detail);
		View::assign('id', $id);
		return view();
    }
	
    //删除
    public function delete()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            $count_course = Db::name('CourseSection')->where([['', 'exp', Db::raw("FIND_IN_SET('{$id}',mids)")]])->count();
            if ($count_course > 0) {
                return to_assign(1, "该内容有关联的课程，无法删除");
            }
            if (Db::name('Material')->where('id', $id)->update(['delete_time' => time()]) !== false) {
                return to_assign(0, "删除成功");
            } else {
                return to_assign(0, "删除失败");
            }
        } else {
            return to_assign(1, "错误的请求");
        }
    }  
}
