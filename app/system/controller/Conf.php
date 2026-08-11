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
use app\system\validate\ConfCheck;
use think\exception\ValidateException;
use think\facade\Db;
use think\facade\View;

class Conf extends BaseController
{
	//配置项列表
    public function datalist()
    {
        if (request()->isAjax()) {
            $where = [];
            $where[] = ['status', '>=', 0];
			$list = Db::name('Config')->where($where)->select();
            return to_assign(0, '', $list);
        } else {
            return view();
        }
    }

    //添加/编辑配置项
    public function add()
    {
        $param = get_params();
        if (request()->isAjax()) {
            try {
                validate(ConfCheck::class)->check($param);
            } catch (ValidateException $e) {
                // 验证失败 输出错误信息
                return to_assign(1, $e->getError());
            }
            if (!empty($param['id']) && $param['id'] > 0) {
                $param['update_time'] = time();
                $res = Db::name('config')->strict(false)->field(true)->update($param);
                if ($res!==false) {
                    add_log('edit', $param['id'], $param);
					return to_assign();
                }
				else{
					return to_assign(1,'操作失败');
				}                
            } else {
                $param['create_time'] = time();
                $insertId = Db::name('Config')->strict(false)->field(true)->insertGetId($param);
                if ($insertId!==false) {
                    add_log('add', $insertId, $param);
					return to_assign();
                }
                else{
					return to_assign(1,'操作失败');
				}
            }
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            if ($id > 0) {
                $config = Db::name('Config')->where(['id' => $id])->find();
                View::assign('config', $config);
            }
            View::assign('id', $id);
            return view();
        }
    }

    //删除配置项
    public function del()
    {
        $id = get_params("id");
		$data = [];
        $data['status'] = '-1';
        $data['id'] = $id;
        $data['update_time'] = time();
        if (Db::name('Config')->update($data) !== false) {
            add_log('delete', $id, $data);
            return to_assign(0, "删除成功");
        } else {
            return to_assign(1, "删除失败");
        }
    }

    //编辑配置信息内容
    public function edit()
    {
        $param = get_params();
        if (request()->isAjax()) {
			$data = [];
            $data['content'] = serialize($param);
            $data['update_time'] = time();
            $data['id'] = $param['id'];
            $res = Db::name('Config')->strict(false)->field(true)->update($data);
            $conf = Db::name('Config')->where('id', $param['id'])->find();
            clear_cache('system_config' . $conf['name']);
            if ($res!==false) {
                add_log('edit', $param['id'], $param);
				return to_assign();
            }
            else{
				return to_assign(1,'操作失败');
			}
        } else {
            $id = isset($param['id']) ? $param['id'] : 0;
            $conf = Db::name('Config')->where('id', $id)->find();
            $module = strtolower(app('http')->getName());
            $class = strtolower(app('request')->controller());
            $action = strtolower(app('request')->action());
            $template = $module . '/view/' . $class . '/' . $conf['name'] . '.html';
            $config = [];
            if ($conf['content']) {
                $config = unserialize($conf['content']);
            }
            View::assign('id', $id);
            View::assign('config', $config);
            if (isTemplate($template)) {
                return view($conf['name']);
            } else {
                return view('../../base/view/common/errortemplate', ['file' => $template]);
            }
        }
    }
}
