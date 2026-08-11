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

//读取试题类型
function get_question_types($id=0)
{
    $type_array = ['未设置','判断题','单选题','多选题','填空题','问答题'];
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

//读取试题类型名称
function question_types_name($id=0)
{
	$question_array = get_question_types();
	return $question_array[$id];
}

//获取等级
function get_question_level($id=0)
{
	$level_array = ['未设置','简单','中等','困难'];
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
function question_level_name($id=0)
{
	$leve_array = get_question_level();
	return $leve_array[$id];
}

/**
 * 随机抽题
 */
function findCombination($items, $targetScore) {
    shuffle($items); // 随机打乱以获得不同解
    $result = [];

    $backtrack = function($start, $current, $sum) use (&$result, &$backtrack, $items, $targetScore) {
        if ($sum === $targetScore) {
            $result = $current;
            return true;
        }

        if ($sum > $targetScore) {
            return false;
        }

        for ($i = $start; $i < count($items); $i++) {
            $item = $items[$i];
            $current[] = $item;
            if ($backtrack($i + 1, $current, $sum + $item['score'])) {
                return true;
            }
            array_pop($current);
        }

        return false;
    };

    if ($backtrack(0, [], 0)) {
        return $result;
    } else {
        return [];
    }
}

//10进制转字母（26）进制
function decimal2ABC($num){
    $ABCstr = "";
    $ten = $num;
    if($ten==0) return "A";
    while($ten!=0){
        $x = $ten%26;
        $ABCstr .= chr(65+$x);
        $ten = intval($ten/26);
    }
    return strrev($ABCstr);
}

//字母（26）进制转10进制
function ABC2decimal($abc){
    $ten = 0;
    $len = strlen($abc);
    for($i=1;$i<=$len;$i++){
		$char = substr($abc,0-$i,1);//反向获取单个字符
        $int = ord($char);
        $ten += ($int-65)*pow(26,$i-1);
    }
    return $ten;
}

function formatS2M($seconds) {
    $durationMinute = round($seconds / 60);
    $durationSecond = round($seconds % 60);
    if ($durationSecond < 10) {
        $durationSecond = '0' . $durationSecond;
    }
    if ($durationMinute < 10) {
        $durationMinute = '0' . $durationMinute;
    }
    return ($durationMinute . ':' . $durationSecond);
}

/**
 * 格式化试题
 * @param  $data 数据
 */
function get_format_questions($questions=[],$type=0){
	foreach($questions as $k => $v){			
		$questions[$k]['index']=$k;
		$questions[$k]['result']=0;
		if($v['type']<4){
			$selects=[];
			$array=explode("⊥",$v['questionselect']);
			if($type==0){
				$a_array=explode(",",$v['questionanswer']);
			}
			foreach($array as $kk => $vv){
				$dot=decimal2ABC($kk);
				$selects[$kk]['dot']=$dot;
				$selects[$kk]['selected']=0;
				$selects[$kk]['title']=$vv;
				if($type==0){
					if(in_array($dot,$a_array)){
						$selects[$kk]['right']=1;
					}else{
						$selects[$kk]['right']=0;
					}
				}
			}
			$questions[$k]['selects']=$selects;
		}
	}
	return $questions;
}
