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
//获取课程内容类型
function get_content_types($id=0)
{
	$types_array = ['未设置','视频','音频','PDF','PPT','Word','图文'];
	if($id==0){
		return $types_array;
	}
	else{
		$news_array=[];
		foreach($types_array as $key => $value){
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

//读取课程内容类型
function content_types_name($id=0)
{
	$types_array = get_content_types();
	return $types_array[$id];
}


//获取学历
function get_education($id=0)
{
	$education_array = ['未设置','本科','硕士','博士'];
	if($id==0){
		return $education_array;
	}
	else{
		$news_array=[];
		foreach($education_array as $key => $value){
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

//读取学历名称
function education_name($id=0)
{
	$education_array = get_education();
	return $education_array[$id];
}

//获取等级
function get_level($id=0)
{
	$level_array = ['未设置','初级','中级','高级'];
	if($id==0){
		return $level_array;
	}
	else{
		$news_array=[];
		foreach($level_array as $key => $value){
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

//读取等级名称
function level_name($id=0)
{
	$leve_array = get_level();
	return $leve_array[$id];
}

//积分+操作
function incIntegral($uid, $integral,$course_id=0,$material_id=0,$exam_id=0)
{
    $integral = intval($integral);
    $res = Db::name('Admin')->where('id', $uid)->inc('integral', $integral)->update();
    if ($res) {
        $data['uid'] = $uid;
        $data['course_id'] = $course_id;
        $data['material_id'] = $material_id;
        $data['exam_id'] = $exam_id;
        $data['integral'] = $integral;
        $data['create_time'] = time();
        $data['type'] = 1;
        Db::name('IntegralRecord')->strict(false)->field(true)->insertGetId($data);
    }
    return $integral;
}

//学分+操作
function incCredit($uid, $credit,$course_id=0)
{
    $credit = intval($credit);
    $res = Db::name('Admin')->where('id', $uid)->inc('credit', $credit)->update();
    if ($res) {
        $data['uid'] = $uid;
        $data['course_id'] = $course_id;
        $data['credit'] = $credit;
        $data['create_time'] = time();
        $data['type'] = 1;
        Db::name('CreditRecord')->strict(false)->field(true)->insertGetId($data);
    }
    return $credit;
}