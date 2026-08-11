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
use app\exam\model\Question as QuestionModel;
use app\exam\model\QuestionCate as QuestionCateModel;
use app\exam\validate\QuestionValidate;
use app\exam\validate\QuestionCateValidate;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Question extends BaseController
{
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new QuestionModel();
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
                $where[] = ['title|desc|questionselect', 'like', '%' . $param['keywords'] . '%'];
            }
			if (!empty($param['cate_id'])) {
                $where[] = ['cate_id', '=', $param['cate_id']];
            }
			if (!empty($param['types'])) {
                $where[] = ['types', '=', $param['types']];
            }
			if (!empty($param['level'])) {
                $where[] = ['level', '=', $param['level']];
            }
            if (!empty($param['keywords'])) {
                $where[] = ['id|title', 'like', '%' . $param['keywords'] . '%'];
            }
            $list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0, '', $list);
        }
        else{
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
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(QuestionValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$this->model->edit($param);
            } else {
                try {
                    validate(QuestionValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$param['admin_id'] = $this->uid;
                $this->model->add($param);
            }
		}
		else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $types = isset($param['types']) ? $param['types'] : 0;
			$question = [];
            if ($id > 0) {
                $question =  $this->model->getById($id);
                if (empty($question)) {
                    return to_assign(1, '考题不存在');
                }
				$types = $question['types'];
            }
			View::assign('id', $id);
			View::assign('types', $types);
            View::assign('question', $question);
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
			View::assign('detail', $detail);
			return view();
		}
		else{
			return view(EEEOR_REPORTING,['code'=>404,'warning'=>'找不到页面']);
		}
    }
	
    /**
    * 设置
    */
    public function  set()
    {
		if (request()->isAjax()) {
			$param = get_params();
			$res = $this->model->strict(false)->field('id,status')->update($param);
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
		} else {
            return to_assign(1, "错误的请求");
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
	
   /**
    * 类型数据列表
    */
    public function cate()
    {
        if (request()->isAjax()) {
			$model = new QuestionCateModel();
            $list = $model->where('delete_time',0)->order('sort asc')->select();
            return to_assign(0, '', $list);
        }
        else{
            return view();
        }
    }
	
    /**
    * 添加/编辑
    */
    public function cate_add()
    {
		$param = get_params();	
        if (request()->isAjax()) {	
			$model = new QuestionCateModel();
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(QuestionCateValidate::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
				$model->edit($param);
            } else {
                try {
                    validate(QuestionCateValidate::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $model->add($param);
            }	 
        }else{
			return view();
		}
    }
	
   /**
    * 删除
    */
    public function  cate_del()
    {
		if (request()->isDelete()) {
			$param = get_params();
			$id = isset($param['id']) ? $param['id'] : 0;
			$type = isset($param['type']) ? $param['type'] : 0;
			$model = new QuestionCateModel();
			$model->delById($id,$type);
		} else {
            return to_assign(1, "错误的请求");
        }
    }

    /**
    * 设置
    */
    public function  cate_set()
    {
		if (request()->isAjax()) {
			$param = get_params();
			$model = new QuestionCateModel();
			$res = $model->strict(false)->field('id,status')->update($param);
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
		} else {
            return to_assign(1, "错误的请求");
        }
    }	

}
