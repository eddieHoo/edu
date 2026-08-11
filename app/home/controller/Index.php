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

namespace app\home\controller;

use app\base\BaseController;
use app\adm\validate\AdminCheck;
use app\system\model\AdminLog;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Index extends BaseController
{	
    public function index()
    {
		$mobile = is_mobile();
		if($mobile){
			return redirect('/qiye/index/index');
		}
		$admin = Db::name('Admin')->where('id',$this->uid)->find();
		if (get_cache('menu' . $this->uid)) {
			$list = get_cache('menu' . $this->uid);
		} else {
			$adminMenu = Db::name('AdminGroup')->where('id', 'in', $admin['role_ids'])->column('rules');
			$adminMenus = [];
			foreach ($adminMenu as $k => $v) {
				$v = explode(',', $v);
				$adminMenus = array_merge($adminMenus, $v);
			}
			$menu = Db::name('AdminRule')->where(['menu' => 1, 'status' => 1])->where('id', 'in', $adminMenus)->order('sort asc,id asc')->select()->toArray();
			$list = list_to_tree($menu);
			\think\facade\Cache::tag('adminMenu')->set('menu' . $this->uid, $list);
		}
		View::assign('menu', $list);
		View::assign('admin',$admin);
		View::assign('system',get_system_config('system'));
		View::assign('web',get_system_config('web'));
		return View();
    }

    public function main()
    {
        $install = false;
        if (file_exists(CMS_ROOT . 'app/install')) {
            $install = true;
        }
		$uid = $this->uid;
		$dids = get_role_departments($uid);
		
        $total = [];		
        $handle = [];		
        $todue = [];		
		$role_ids = Db::name('Admin')->where('id',$uid)->value('role_ids');
		$adminLayout = Db::name('AdminGroup')->where('id', 'in', $role_ids)->column('layouts');
		$adminLayouts = [];
		foreach ($adminLayout as $k => $v) {
			$v = explode(',', $v);
			$adminLayouts = array_merge($adminLayouts, $v);
		}
		$layouts = get_config('layout');
		$layout_selected = [];
		foreach ($layouts as $key =>$vo) {
			if (!empty($adminLayouts) and in_array($vo['id'], $adminLayouts)) {
				$layout_selected[] = $vo;
			}
		}
		View::assign('layout_selected',$layout_selected);
        View::assign('total', $total);
        View::assign('handle', $handle);
        View::assign('todue', $todue);
        View::assign('install', $install);
        View::assign('TP_VERSION', \think\facade\App::version());
        return View();
    }
	
	//权限不足
	public function role()
    {
		return View('../../base/view/common/roletemplate');
	}
	//通讯录
	public function contacts_book()
    {
        if (request()->isAjax()) {
            $param = get_params();
            $where = array();
            $whereOr = array();
            if (!empty($param['keywords'])) {
                $where[] = ['a.id|a.username|a.name|a.nickname|a.mobile|a.desc', 'like', '%' . $param['keywords'] . '%'];
            }
            if (!empty($param['did'])) {
				$admin_array = Db::name('DepartmentAdmin')->where('department_id',$param['did'])->column('admin_id');
				$map1=[
					['a.id','in',$admin_array],
				];
				$map2=[
					['a.did', '=', $param['did']],
				];
				$whereOr =[$map1,$map2];
            }
            $where[] = ['a.status', '=', 1];
            $where[] = ['a.id', '>', 1];
			$admin = \app\user\model\Admin::alias('a')
				->with('departments')
				->field('a.*,p.title as position,d.title as department')
				->join('Department d', 'd.id = a.did','left')
				->join('Position p', 'p.id = a.position_id','left')
				->where($where)
				->where(function ($query) use($whereOr) {
					if (!empty($whereOr)){
						$query->whereOr($whereOr);
					}
				})
				->paginate(intval($this->pageSize))
				->order('a.id desc')
                ->each(function ($item, $key) {
					//遍历次要部门数据
					$departments = $item->departments->toArray();
					if(empty($departments)){
						$item->departments = '-';
					}
					else{
						$item->departments = split_array_field($departments,'title');
					}
					if($item->is_hide ==1){
						$item->mobile = hidetel($item->mobile);
						$item->email = hidetel($item->email);
					}
                    $item->entry_time = empty($item->entry_time) ? '-' : to_date($item->entry_time,'Y-m-d',);
                });
            return table_assign(0, '', $admin);
        } else {
            return view();
        }
    }
	

    //修改个人信息
    public function edit_personal()
    {
		if (request()->isAjax()) {
            $param = get_params();
            $uid = $this->uid;
            Db::name('Admin')->where(['id' => $uid])->strict(false)->field(true)->update($param);
            return to_assign();
        }
		else{
			View::assign('admin',get_admin($this->uid));
			return view();
		}
    }

    //修改密码
    public function edit_password()
    {
		if (request()->isAjax()) {			
			$param = get_params();			
            try {
                validate(AdminCheck::class)->scene('editPwd')->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
            $uid = $this->uid;
			
			$admin = Db::name('Admin')->where(['id' => $uid])->find();
			$old_psw = set_password($param['old_pwd'], $admin['salt']);
			if ($admin['pwd'] != $old_psw) {
				return to_assign(1, '旧密码错误');
			}

			$salt = set_salt(20);
			$new_pwd = set_password($param['pwd'], $salt);
			$data = [
				'reg_pwd' => '',
				'salt' => $salt,
				'pwd' => $new_pwd,
				'update_time' => time(),
			];
            Db::name('Admin')->where(['id' => $uid])->strict(false)->field(true)->update($data);
            return to_assign();
        }
		else{
			View::assign('admin',get_admin($this->uid));
			return view();
		}
    }
	
    //系统操作日志
    public function log_list()
    {
		if (request()->isAjax()) {
			$param = get_params();
			$log = new AdminLog();
			$content = $log->get_log_list($param);
			return table_assign(0, '', $content);
		}else{
			return view();
		}
    }
	
	//设置theme
	public function set_theme()
    {
        if (request()->isAjax()) {
            $param = get_params();
			Db::name('Admin')->where('id',$this->uid)->update(['theme'=>$param['theme']]);
            return to_assign();
        }
		else{
			return to_assign(1,'操作错误');
		}
    }
}
