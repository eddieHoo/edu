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

namespace app\system\controller;

use app\base\BaseController;
use app\system\validate\ModuleCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Module extends BaseController
{
    public function datalist()
    {
        if (request()->isAjax()) {
            $module = Db::name('AdminModule')->select();
            return to_assign(0, '', $module);
        } else {
			$sys_module = Db::name('AdminModule')->select()->toArray();
			View::assign('sys_module', $sys_module);
            return view();
        }
    }
	
	//新增/编辑模块
    public function add()
    {
		$param = get_params();
        if (request()->isAjax()) {
			if($this->uid!=1){
				return to_assign(1,'只有系统超级管理员才有权限新增或编辑模块！');
			}
            if (!empty($param['id']) && $param['id'] > 0) {
                try {
                    validate(ModuleCheck::class)->scene('edit')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                Db::name('AdminModule')->where(['id' => $param['id']])->strict(false)->field(true)->update($param);
                add_log('edit', $param['id'], $param);
            } else {
                try {
                    validate(ModuleCheck::class)->scene('add')->check($param);
                } catch (ValidateException $e) {
                    // 验证失败 输出错误信息
                    return to_assign(1, $e->getError());
                }
                $mid = Db::name('AdminModule')->strict(false)->field(true)->insertGetId($param);
                add_log('add', $mid, $param);
            }
			return to_assign();
        } else {
			$id = isset($param['id']) ? $param['id'] : 0;
			$module=[];
			if ($id > 0) {
                $module = Db::name('AdminModule')->where(['id' => $id])->find();
            }
			View::assign('id', $id);
			View::assign('module', $module);
            return view();
        }
    }
	
	//递归插入菜单数据
	protected function add_rule($data, $pid=0,$sort=0)
	{
		foreach($data as $k => $v)
		{
			$rule=[
				'title'  => $v['title'],
				'name'   => $v['name'],
				'src'    => $v['src'],
				'module' => $v['module'],
				'menu'   => $v['menu'],
				'icon'   => $v['icon'],
				'pid'    => $pid,
				'sort'   => $sort,
				'create_time' => time()
			];
			$new_id = Db::name('AdminRule')->strict(false)->field(true)->insertGetId($rule);
			if(!empty($v['son'] && $new_id)){
				$this->add_rule($v['son'],$new_id);			
			}
		}
	}

    //删除
    public function del()
    {
		if($this->uid!=1){
			return to_assign(1,'只有系统超级管理员才有权限删除模块！');
		}
        $param = get_params();
		$module = Db::name('AdminModule')->where('id',$param['id'])->find();
		if($module['type'] == 1){
			return to_assign(1,'系统模块不能删除');
		}
		$param['update_time']= time();
		$res = Db::name('AdminModule')->where('id',$param['id'])->delete();
		if($res!==false){
			add_log('delete', $module['id'], $param);
			return to_assign();
		}
		else{
			return to_assign(1,'操作失败');
		}
    }

    //卸载
    public function uninstall()
    {
		if($this->uid!=1){
			return to_assign(1,'只有系统超级管理员才有权限卸载模块！');
		}
        $param = get_params();
		$module = Db::name('AdminModule')->where('name',$param['name'])->find();
		if($module['type'] == 1){
			return to_assign(1,'系统模块不能卸载');
		}
		$param['update_time']= time();
		$res = Db::name('AdminModule')->where('name',$param['name'])->update(['status'=>0]);
		if($res!==false){
			Db::name('AdminRule')->strict(false)->where('module',$module['name'])->update(['status'=>0]);
			// 删除后台节点缓存
            clear_cache('adminRules');
			add_log('uninstall', $module['id'], $param);
			return to_assign();
		}
		else{
			return to_assign(1,'操作失败');
		}
    }
	
	//恢复
    public function recovery()
    {
		if($this->uid!=1){
			return to_assign(1,'只有系统超级管理员才有权限恢复模块！');
		}
        $param = get_params();
		$module = Db::name('AdminModule')->where('name',$param['name'])->find();
		$param['update_time']= time();
		$res = Db::name('AdminModule')->where('name',$param['name'])->update(['status'=>1]);
		if($res!==false){
			Db::name('AdminRule')->strict(false)->where('module',$module['name'])->update(['status'=>1]);
			// 删除后台节点缓存
            clear_cache('adminRules');
			add_log('recovery', $module['id'], $param);
			return to_assign();
		}
		else{
			return to_assign(1,'操作失败');
		}
    }
	
	
    //编辑配置信息
    public function auth()
    {
        $param = get_params();
        if (request()->isAjax()) {
            $param['update_time'] = time();
            $res = Db::name('AdminModule')->strict(false)->field(true)->update($param);
            if ($res) {
                add_log('edit', $param['id'], $param);
            }
            return to_assign();
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $detail = $this->auth_detail($id);
            $module = strtolower(app('http')->getName());
            $class = strtolower(app('request')->controller());
            $action = strtolower(app('request')->action());
            $template = $module . '/view/' . $class . '/' . $detail['name'] . '_admin.html';
            View::assign('detail', $detail);
            if (isTemplate($template)) {
                return view($detail['name'].'_admin');
            } else {
                return view('../../base/view/common/errortemplate', ['file' => $template]);
            }
        }
    }
	
	public function auth_detail($id)
    {
        $detail = Db::name('AdminModule')->where('id',$id)->find();
		if($detail['name'] =='home'){			
			$conf_1_str = Db::name('Admin')->where('id', 'in', $detail['conf_1'])->column('name');
			$detail['conf_1_str'] = implode(',', $conf_1_str);
		}
		if($detail['name'] =='system'){
			$conf_1_str = Db::name('Admin')->where('id', 'in', $detail['conf_1'])->column('name');
            $detail['conf_1_str'] = implode(',', $conf_1_str);
		}
		if($detail['name'] =='adm'){
			$conf_1_str = Db::name('Admin')->where('id', 'in', $detail['conf_1'])->column('name');
            $detail['conf_1_str'] = implode(',', $conf_1_str);
		}
		if($detail['name'] =='course'){
			$conf_1_str = Db::name('Admin')->where('id', 'in', $detail['conf_1'])->column('name');
            $detail['conf_1_str'] = implode(',', $conf_1_str);
		}
		if($detail['name'] =='train'){
			$conf_1_str = Db::name('Admin')->where('id', 'in', $detail['conf_1'])->column('name');
            $detail['conf_1_str'] = implode(',', $conf_1_str);
		}
		if($detail['name'] =='exam'){
			$conf_1_str = Db::name('Admin')->where('id', 'in', $detail['conf_1'])->column('name');
            $detail['conf_1_str'] = implode(',', $conf_1_str);
		}if($detail['name'] =='honor'){
			$conf_1_str = Db::name('Admin')->where('id', 'in', $detail['conf_1'])->column('name');
            $detail['conf_1_str'] = implode(',', $conf_1_str);
		}
		if($detail['name'] =='disk'){
			$conf_1_str = Db::name('Admin')->where('id', 'in', $detail['conf_1'])->column('name');
            $detail['conf_1_str'] = implode(',', $conf_1_str);
		}
		return $detail;
    }
	
}
