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

use think\facade\Db;
//读取证书类型
function get_certificate_types($id=0)
{
    $type_array = ['未设置','课程结业证书','培训结业证书','考试合格证书'];
	if($id==0){
		return $type_array;
	}
	else{
		$news_array=[];
		foreach($type_array as $key => $value){
			if($key>0){
				$news_array[]=array(
					'id'=>$key,
					'title'=>$value,
				);
			}
		}
		return $news_array;
	}
}

//读取证书类型名称
function certificate_types_name($id=0)
{
	$certificate_array = get_certificate_types();
	return $certificate_array[$id];
}