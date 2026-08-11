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
namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;
use app\user\model\Admin;
use app\customer\model\Customer;
use app\exam\model\Question;
use avatars\MDAvatars;
use Overtrue\Pinyin\Pinyin;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as Shared;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class Import extends BaseController
{
    //生成头像
    public function to_avatars($char)
    {
        $defaultData = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
            'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'S', 'Y', 'Z',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            '零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖', '拾',
            '一', '二', '三', '四', '五', '六', '七', '八', '九', '十');
        if (isset($char)) {
            $Char = $char;
        } else {
            $Char = $defaultData[mt_rand(0, count($defaultData) - 1)];
        }
        $OutputSize = min(512, empty($_GET['size']) ? 36 : intval($_GET['size']));

        $Avatar = new MDAvatars($Char, 256, 1);
        $avatar_name = '/avatars/avatar_256_' . set_salt(10) . time() . '.png';
        $path = get_config('filesystem.disks.public.url') . $avatar_name;
        $res = $Avatar->Save('.' . $path, 256);
        $Avatar->Free();
        return $path;
    }
	
    //登录名校验
    public function check_name($name,$arr)
    {
        if(in_array($name,$arr)){
			$name = $this->check_name($name.'1',$arr);
		}
		return $name;       
    }
	
	//导入员工
	public function import_admin(){
        // 获取表单上传文件
        $file[]= request()->file('file');
		if($this->uid>1){
			return to_assign(1,'该操作只能是超级管理员有权限操作');
		}
        try {
            // 验证文件大小，名称等是否正确
            validate(['file' => 'filesize:51200|fileExt:xls,xlsx'])->check($file);
			// 日期前綴
			 $dataPath = date('Ym');
			 $md5 = $file[0]->hash('md5');
			 $savename = \think\facade\Filesystem::disk('public')->putFile($dataPath, $file[0], function () use ($md5) {
				 return $md5;
			 });
            $fileExtendName = substr(strrchr($savename, '.'), 1);
            // 有Xls和Xlsx格式两种
            if ($fileExtendName == 'xlsx') {
                $objReader = IOFactory::createReader('Xlsx');
            } else {
                $objReader = IOFactory::createReader('Xls');
            }
            $objReader->setReadDataOnly(TRUE);
			$path = get_config('filesystem.disks.public.url');
            // 读取文件，tp6默认上传的文件，在runtime的相应目录下，可根据实际情况自己更改
            $objPHPExcel = $objReader->load('.'.$path . '/' .$savename);
            $sheet = $objPHPExcel->getSheet(0);   //excel中的第一张sheet
            $highestRow = $sheet->getHighestRow();       // 取得总行数
            $highestColumn = $sheet->getHighestColumn();   // 取得总列数
            Coordinate::columnIndexFromString($highestColumn);
            $lines = $highestRow - 1;
            if ($lines <= 0) {
				return to_assign(1, '数据不能为空');
                exit();
            }
			$sex_array=['未知','男','女'];
			$type_array=['未知','正式','试用','实习'];
			$mobile_array = Db::name('Admin')->where([['status','>=',0]])->column('mobile');
			$email_array = Db::name('Admin')->where([['status','>=',0]])->column('email');
			$username_array = Db::name('Admin')->where([['status','>=',0]])->column('username');
			$department_array = Db::name('Department')->where(['status' => 1])->column('title', 'id');
			$position_array = Db::name('Position')->where(['status' => 1])->column('title', 'id');
            //循环读取excel表格，整合成数组。如果是不指定key的二维，就用$data[i][j]表示。
            for ($j = 3; $j <= $highestRow; $j++) {
				$salt = set_salt(20);
				$reg_pwd  = '123456';
				$name = $objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue();
				if(empty($name)){
					continue;
				}
				$char = mb_substr($name, 0, 1, 'utf-8');
				$sex = array_search_plus($sex_array,$objPHPExcel->getActiveSheet()->getCell("D" . $j)->getValue());
				$department = array_search_plus($department_array,$objPHPExcel->getActiveSheet()->getCell("E" . $j)->getValue());
				$position = array_search_plus($position_array,$objPHPExcel->getActiveSheet()->getCell("f" . $j)->getValue());
				$type = array_search_plus($type_array,$objPHPExcel->getActiveSheet()->getCell("G" . $j)->getValue());
				$username = Pinyin::name($name,'none')->join('');
				//$username = implode('-', $pinyinname);
				$mobile = $objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue();
				$email = $objPHPExcel->getActiveSheet()->getCell("C" . $j)->getValue();
				$file_check['mobile'] = $mobile;
				$file_check['email'] = $email;
				$validate_mobile = \think\facade\Validate::rule([
					'mobile' => 'require|mobile',
				]);
				$validate_email = \think\facade\Validate::rule([
					'email' => 'email',
				]);
				if (!$validate_mobile->check($file_check)) {
					return to_assign(1, '第'.($j - 2).'行的手机号码的格式错误');
				}
				else{
					if(in_array($mobile,$mobile_array)){
						return to_assign(1, '第'.($j - 2).'行的手机号码已存在或者重复');
					}
					else{
						array_push($mobile_array,$mobile);
					}
				}
				if(!empty($email)){
					if (!$validate_email->check($file_check)) {
						return to_assign(1, '第'.($j - 2).'行的电子邮箱的格式错误');
					}
					else{
						if(in_array($email,$email_array)){
							return to_assign(1, '第'.($j - 2).'行的电子邮箱已存在或者重复');
						}
						else{
							array_push($email_array,$email);
						}
					}
				}
				else{
					$email='';
				}
				if(empty($department)){
					return to_assign(1, '第'.($j - 2).'行的所在部门错误');
				}
				if(empty($position)){
					return to_assign(1, '第'.($j - 2).'行的所属职位错误');
				}
				
				$newusername = $this->check_name($username,$username_array);
				array_push($username_array,$newusername);				
                $data[$j - 3] = [		
                    'name' => $name,
                    'nickname' => $name,
                    'mobile' => $mobile,
                    'email' => $email,
                    'sex' => $sex,
                    'did' => $department,
                    'position_id' => $position,
                    'type' => $type,
					'entry_time' => Shared::excelToTimestamp($objPHPExcel->getActiveSheet()->getCell("H" . $j)->getValue(),'Asia/Shanghai'),
					'username' => $newusername,
                    'salt' => $salt,
					'pwd' => set_password($reg_pwd, $salt),
                    'reg_pwd' => $reg_pwd,
                    'thumb' => $this->to_avatars($char)
                ];
            }
           //dd($data);exit;
			$count=0;
			foreach ($data as $a => $aa) {	
				$aid = Admin::strict(false)->field(true)->insertGetId($aa);
				if($aid>0){
					//Db::name('DepartmentAdmin')->insert(['admin_id'=>$aid,'department_id'=>$aa['did'],'create_time' => time()]);
					$count++;
				}
			}
            return to_assign(0, '共成功导入了'.$count.'条员工数据');
        } catch (\think\exception\ValidateException $e) {
			return to_assign(1, $e->getMessage());
        }
    }
	
	//导入试题
	public function import_question(){
        // 获取表单上传文件
        $file[]= request()->file('file');
		
		$param = get_params();
        try {
            // 验证文件大小，名称等是否正确
            validate(['file' => 'filesize:51200|fileExt:xls,xlsx'])->check($file);
			// 日期前綴
			 $dataPath = date('Ym');
			 $md5 = $file[0]->hash('md5');
			 $savename = \think\facade\Filesystem::disk('public')->putFile($dataPath, $file[0], function () use ($md5) {
				 return $md5;
			 });
            $fileExtendName = substr(strrchr($savename, '.'), 1);
            // 有Xls和Xlsx格式两种
            if ($fileExtendName == 'xlsx') {
                $objReader = IOFactory::createReader('Xlsx');
            } else {
                $objReader = IOFactory::createReader('Xls');
            }
            $objReader->setReadDataOnly(TRUE);
			$path = get_config('filesystem.disks.public.url');
            // 读取文件，tp6默认上传的文件，在runtime的相应目录下，可根据实际情况自己更改
            $objPHPExcel = $objReader->load('.'.$path . '/' .$savename);
            //$objPHPExcel = $objReader->load('./storage/202209/d11544d20b3ca1c1a5f8ce799c3b2433.xlsx');
            $sheet = $objPHPExcel->getSheet(0);   //excel中的第一张sheet
            $highestRow = $sheet->getHighestRow();       // 取得总行数
            $highestColumn = $sheet->getHighestColumn();   // 取得总列数
            Coordinate::columnIndexFromString($highestColumn);
            $lines = $highestRow - 1;
            if ($lines <= 0) {
				return to_assign(1, '数据不能为空');
                exit();
            }
			$title_array = [];
            //循环读取excel表格，整合成数组。如果是不指定key的二维，就用$data[i][j]表示。
			$cate_array = Db::name('QuestionCate')->where(['status' => 1])->column('title', 'id');
			$types_array = [1=>'判断题',2=>'单选题',3=>'多选题',4=>'填空题',5=>'简答题'];
			$level_array = [1=>'简单',2=>'中等',3=>'困难'];
			
			$data=[];
            //循环读取excel表格，整合成数组。如果是不指定key的二维，就用$data[i][j]表示。
            for ($j = 2; $j <= $highestRow; $j++) {
				$types = array_search_plus($types_array,$objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue());
				$cate_id = array_search_plus($cate_array,$objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue());
				$level = array_search_plus($level_array,$objPHPExcel->getActiveSheet()->getCell("C" . $j)->getValue());
				
				$score = $objPHPExcel->getActiveSheet()->getCell("D" . $j)->getValue();
				$title = $objPHPExcel->getActiveSheet()->getCell("E" . $j)->getValue();	
				$desc = $objPHPExcel->getActiveSheet()->getCell("F" . $j)->getValue();
				
				$selectA = $objPHPExcel->getActiveSheet()->getCell("G" . $j)->getValue();	
				$selectB = $objPHPExcel->getActiveSheet()->getCell("H" . $j)->getValue();	
				$selectC = $objPHPExcel->getActiveSheet()->getCell("I" . $j)->getValue();	
				$selectD = $objPHPExcel->getActiveSheet()->getCell("J" . $j)->getValue();	
				$selectE = $objPHPExcel->getActiveSheet()->getCell("K" . $j)->getValue();	
				$selectF = $objPHPExcel->getActiveSheet()->getCell("L" . $j)->getValue();	
				$selectG = $objPHPExcel->getActiveSheet()->getCell("M" . $j)->getValue();	
				$selectH = $objPHPExcel->getActiveSheet()->getCell("N" . $j)->getValue();	
				
				$questionanswer = $objPHPExcel->getActiveSheet()->getCell("O" . $j)->getValue();				
				$answerdescribe = $objPHPExcel->getActiveSheet()->getCell("P" . $j)->getValue();
				
				if(empty($title)){
					continue;
				}
				
				$count_title = Db::name('Question')->where([['title','=',$title],['delete_time','=',0]])->count();
				if($count_title>0){
					return to_assign(1, '第'.$j.'行的试题题干【'.$title.'】已经存在题库中');
				}
				
				if(in_array($title,$title_array)){
					return to_assign(1, '导入的数据表第'.$j.'行【'.$title.'】存在相同的试题题干信息，请删除再操作');
				}
				array_push($title_array,$title);				
				
				if(empty($types) || $types==false){
					return to_assign(1, '第'.$j.'行【'.$title.'】的试题类型错误');
				}
				if(empty($cate_id) || $cate_id==false){
					return to_assign(1, '第'.$j.'行【'.$title.'】的试题所属分类错误');
				}
				if(empty($level) || $level==false){
					return to_assign(1, '第'.$j.'行【'.$title.'】的试题难易度错误');
				}
				if(empty($score) || !is_int($score)){
					return to_assign(1, '第'.$j.'行【'.$title.'】的试题分数填写错误');
				}
				if(empty($title)){
					return to_assign(1, '第'.$j.'行【'.$title.'】的试题题干不能为空');
				}
				
				$questionselect='';
				if(in_array($types,[2,3])){
					if(empty($selectA)){
						return to_assign(1, '第'.$j.'行【'.$title.'】的试题选项A为空');
					}
					if(empty($selectB)){
						return to_assign(1, '第'.$j.'行【'.$title.'】的试题选项B为空');
					}
					$questionselect = $selectA.'⊥'.$selectB;
					
					if(!empty($selectC)){
						$questionselect.='⊥'.$selectC;
					}
					if(!empty($selectD)){
						$questionselect.='⊥'.$selectD;
					}
					if(!empty($selectE)){
						$questionselect.='⊥'.$selectE;
					}
					if(!empty($selectF)){
						$questionselect.='⊥'.$selectF;
					}
					if(!empty($selectG)){
						$questionselect.='⊥'.$selectG;
					}
					if(!empty($selectH)){
						$questionselect.='⊥'.$selectH;
					}
				}
				
				if($types==1){
					$questionselect='正确⊥错误';				
				}
				
				if($types==5){
					if(empty($questionanswer)){
						$questionanswer='';
					}					
				}
				else{
					if(empty($questionanswer)){
						return to_assign(1, '第'.$j.'行【'.$title.'】的答案不能为空');
					}
					else{
						if($types==1){
							if(($questionanswer!='正确' && $questionanswer!='错误')){
								return to_assign(1, '第'.$j.'行【'.$title.'】是判断题，答案必须只能是【正确】或者【错误】，且不能有空格');
							}
							else{
								if($questionanswer=='正确'){
									$questionanswer='A';
								}
								if($questionanswer=='错误'){
									$questionanswer='B';
								}
							}
						}
                        if(in_array($types,[2,3]) && preg_match('/^[A-Z]+$/', $questionanswer) == false){
							return to_assign(1, '第'.$j.'行【'.$title.'】是选择题，答案必须为大写字母，且不能有空格');
						}
						if($types==2 && strlen($questionanswer)>1){
							return to_assign(1, '第'.$j.'行【'.$title.'】是单选题，答案必须只能是一项，且不能有空格');
						}
						if($types==3 && strlen($questionanswer)<2){
							return to_assign(1, '第'.$j.'行【'.$title.'】是多选题，答案必须至少两项，且不能有空格');
						}
					}
				}
				
                $data[] = [		
                    'types' => $types,
                    'cate_id' => $cate_id,
                    'level' => $level,
                    'score' => $score,
                    'title' => $title,
                    'desc' => $desc,
                    'questionselect' => $questionselect,
                    'questionanswer' => $questionanswer,
                    'answerdescribe' => $answerdescribe,
                    'admin_id' => $this->uid,
                    'create_time' => time(),
                    'update_time' => time()
                ];
            }
           //dd($data);exit;
            // 批量添加数据
            if ((new Question())->saveAll($data)) {
				$count = count($data);
                return to_assign(0, '成功导入'.$count.'条记录');
            }
			else{
				return to_assign(1, '导入失败，请检查excel文件再试');
			}
        } catch (\think\exception\ValidateException $e) {
			return to_assign(1, $e->getMessage());
        }
    }
}
