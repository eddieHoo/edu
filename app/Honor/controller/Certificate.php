<?php

declare (strict_types = 1);

namespace app\honor\controller;

use app\base\BaseController;
use app\honor\model\Certificate as CertificateModel;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Certificate extends BaseController
{		
	/**
     * 构造函数
     */
	protected $model;
    public function __construct()
    {
		parent::__construct(); // 调用父类构造函数
        $this->model = new CertificateModel();
    }
	
	public function datalist()
    {
        if (request()->isAjax()) {
            $param = get_params();
			$where = [];
			$whereOr = [];
			if (!empty($param['keywords'])) {
                $where[] = ['title', 'like', '%' . $param['keywords'] . '%'];
            }
			$where[] = ['delete_time', '=', 0];
			$list = $this->model->datalist($param,$where,$whereOr);
            return table_assign(0,'', $list);
        } else {
            return view();
        }
    }	
	
	//证书列表
    public function honorlist($param=[],$where=[],$whereOr=[])
    {
        if (request()->isAjax()) {
            $param = get_params();
			$where = [];
			$whereOr = [];
			if (!empty($param['keywords'])) {
                $where[] = ['c.title|c.code', 'like', '%' . $param['keywords'] . '%'];
            }
			$list = $this->model->mylist($param,$where,$whereOr);
            return table_assign(0,'', $list);
        } else {
            return view();
        }
    }
	
	//我的证书
    public function mylist($param=[],$where=[],$whereOr=[])
    {
        if (request()->isAjax()) {
            $param = get_params();
			$where = [];
			$whereOr = [];
			if (!empty($param['keywords'])) {
                $where[] = ['c.title|c.code', 'like', '%' . $param['keywords'] . '%'];
            }
			$where[] = ['cu.user_id', '=', $this->uid];
			$list = $this->model->mylist($param,$where,$whereOr);
            return table_assign(0,'', $list);
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
                $this->model->edit($param);
            } else {
                $param['admin_id'] = $this->uid;
                $this->model->add($param);
            }
        }
		else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $type = isset($param['type']) ? $param['type'] : 1;
			$detail=[
				'type'=>$type,
				'name'=>'结业证书',
				'content_top'=>'参与《中层领导力》课程的学习，已完成了该课程的学习，共计50课时。',
				'content_bottom'=>'给予结业，特发此证，以资鼓励！',
				'time'=>date('Y-m-d',time()),
				'code'=>'CCJY'
			];
			if($type ==2){
				$detail=[
					'type'=>$type,
					'name'=>'结业证书',
					'content_top'=>'参与《中层领导力》面授培训的学习，已完成了该培训的学习，共计3课时。',
					'content_bottom'=>'给予结业，特发此证，以资鼓励！',
					'time'=>date('Y-m-d',time()),
					'code'=>'PXJY'
				];
			}
			if($type ==3){
				$detail=[
					'type'=>$type,
					'name'=>'成绩合格证书',
					'content_top'=>'参加公司内部举办的《中层领导力》能力水平考试。。',
					'content_bottom'=>'考试成绩合格，特发此证。',
					'time'=>date('Y-m-d',time()),
					'code'=>'CJHG'
				];
			}
            if ($id > 0) {
                $detail = $this->model->getById($id);
                if (empty($detail)) {
                    return to_assign(1, '证书模板不存在');
                }
				$detail['time']=date('Y-m-d',time());
				View::assign('detail', $detail);
				return view('edit');
            }
			View::assign('detail', $detail);
			return view();
        }
    }	
	
	//设置
    public function check()
    {
		$param = get_params();
        $res = Db::name('Certificate')->strict(false)->field('id,status')->update($param);
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
	
    //删除
    public function del()
    {
        if (request()->isDelete()) {
            $id = get_params("id");
            if (Db::name('Certificate')->where('id', $id)->update(['delete_time' => time()]) !== false) {
                return to_assign(0, "删除成功");
            } else {
                return to_assign(0, "删除失败");
            }
        } else {
            return to_assign(1, "错误的请求");
        }
    }   
	
	
    //证书授予列表
    public function users()
    {
        $param = get_params();
		if (request()->isAjax()) {
			$where[] = ['cu.certificate_id', '=', $param['id']];
			$rows = empty($param['limit']) ? get_config('app.page_size') : $param['limit'];
			$list = Db::name('CertificateUsers')
				->field('cu.*,a.name as user,c.code')
				->alias('cu')
				->join('Admin a', 'a.id = cu.user_id', 'LEFT')
				->join('Certificate c', 'c.id = cu.certificate_id', 'LEFT')
				->where($where)
				->order('cu.id desc')
				->paginate(['list_rows'=> $rows])
				->each(function ($item, $key) {
					$item['create_time'] = date('Y-m-d H:i', $item['create_time']);
					return $item;
				});
			return table_assign(0, '', $list);
		}
		else{
			$id = isset($param['id']) ? $param['id'] : 0;
			$detail = $this->model->getById($id);
			$ids = Db::name('CertificateUsers')->where('certificate_id',$id)->column('user_id');
			$detail['ids'] = implode(',',$ids);
			View::assign('detail', $detail);
            return view();
        }
    }

	//证书授予学员操作
    public function check_user()
    {
        $param = get_params();
		if (request()->isPost()) {
			$idArray = explode(",",$param['ids']);
			foreach ($idArray as $k => $v) {
				$data = [];
				$data['user_id'] = $v;
				$data['certificate_id'] = $param['certificate_id'];
				$data['admin_id'] = $this->uid;
				$data['create_time'] = time();
				Db::name('CertificateUsers')->strict(false)->insertGetId($data);
				add_log('grant', $v, $data);
			}
			return to_assign(0, "操作成功");
		}
		if (request()->isDelete()) {
            $id = get_params("id");
            if (Db::name('CertificateUsers')->where('id', $id)->delete() !== false) {
				add_log('back', $id, $param);
                return to_assign(0, "删除成功");
            } else {
                return to_assign(0, "删除失败");
            }
        }
    }
   
}
