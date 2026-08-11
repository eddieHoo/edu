<?php
/**
+-----------------------------------------------------------------------------------------------
* GouGuOPEN [ 左手研发，右手开源，未来可期！]
+-----------------------------------------------------------------------------------------------
* @Copyright (c) http://www.gouguoa.com All rights reserved.
+-----------------------------------------------------------------------------------------------
* @Licensed 勾股OA，开源且可免费使用，但并不是自由软件，未经授权许可不能去除勾股OA的相关版权信息
+-----------------------------------------------------------------------------------------------
* @Author 勾股工作室 <hdm58@qq.com>
+-----------------------------------------------------------------------------------------------
*/
declare (strict_types = 1);
namespace app\api\controller;

use app\api\BaseController;
use think\facade\Db;
use think\facade\View;
use Mpdf\Mpdf;
use PhpOffice\PhpWord\TemplateProcessor;
use app\adm\model\MeetingRecords;

class Export extends BaseController
{	
    public function pdf($types='',$id=0)
    {
		$name='PDF文件';
		$is_header=true;
		$check_record=true;
		$is_page=false;
		$logo = CMS_ROOT.'public/static/home/images/logo.png';
		//会议记录
		if($types=='meeting'){
			$model = new MeetingRecords();
			$detail= $model->getById($id);
			$name = date('Ymd',$detail['meeting_date']).$detail['did_name'].'_会议纪要';
			$check_record=false;
		}
		//员工档案
		if($types=='files'){
			$detail = get_admin($id);
			$detail['pname'] = Db::name('Admin')->where('id',$detail['pid'])->value('name');
			$detail['position'] = Db::name('Position')->where('id',$detail['position_id'])->value('title');
			$detail['department'] = Db::name('Department')->where('id',$detail['did'])->value('title');
			$department_ids = Db::name('DepartmentAdmin')->where('admin_id',$id)->column('department_id');
			$department_names = Db::name('Department')->whereIn('id',$department_ids)->column('title');
			$detail['department_names'] = implode(',',$department_names);
			if($detail['file_ids'] !=''){
				$file_array = Db::name('File')->where('id','in',$detail['file_ids'])->select();
				$detail['file_array'] = $file_array;
			}
			$edu = Db::name('AdminProfiles')->where(['types'=>1,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
			$work = Db::name('AdminProfiles')->where(['types'=>2,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
			$certificate = Db::name('AdminProfiles')->where(['types'=>3,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
			$skills = Db::name('AdminProfiles')->where(['types'=>4,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
			$language = Db::name('AdminProfiles')->where(['types'=>5,'admin_id'=>$id,'delete_time'=>0])->select()->toArray();
			View::assign('edu', $edu);
			View::assign('work', $work);
			View::assign('skills', $skills);
			View::assign('certificate', $certificate);
			View::assign('language', $language);
			View::assign('detail', $detail);
			$name = $detail['name'].'_档案信息';
			$check_record=false;
			$is_page=true;
		}
		
		$detail['pdf_admin'] = Db::name('Admin')->where([['id','=',$this->uid]])->value('name');
		$detail['pdf_time'] = date('Y-m-d H:i:s');
		//var_dump($detail);exit;
		View::assign('detail', $detail);
		View::assign('logo', $logo);
        $html = View::fetch($types);
		$time = time();
        //tempDir指定临时文件目录，需要有可写入的权限，否则会报错
        $mpdf = new Mpdf([
			'mode'=>'zh',//或者utf-8
            'format' => 'A4',
			'margin_top'=>23,
			'margin_bottom'=>19,
			//重新定义字体路径
			'fontDir' => [
				CMS_ROOT . "public/static/font/"
			],
			//重新定义默认字体
			'fontdata' => [
				"sun-exta" => [
					'R'  => 'MiSans-Regular.ttf', // regular font
					'B'  => 'MiSans-Bold.ttf', // optional: bold font
					'I'  => 'MiSans-Regular.ttf', // optional: italic font
					'BI' => 'MiSans-Bold.ttf', // optional: bold-italic font 
					'useKashida' => 75,
					'sip-ext' => 'MiSans-Regular.ttf', /* SIP=Plane2 Unicode (extension B) */
				]
			],
            'tempDir' => CMS_ROOT . "public/storage/pdf/"
        ]);
        $mpdf->SetDisplayMode('fullpage');
        //自动分析录入内容字体
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
		//文件名称
		$filename = $name."_".$time.".pdf";
        //文章pdf文件存储路径
		$path = CMS_ROOT . "public" . "/storage/pdf/".$filename;
		$pageNumber = count($mpdf->pages);
		$header= '<table width="100%" style="margin:0;padding:0"><tr> 
<td width="50%" style="text-align:left;font-size:12px; color:#999999;margin:0;padding:0"><img src="'.$logo.'" height="36" /></td> 
<td width="50%" style="text-align:right;font-size:12px; color:#999999;margin:0;padding:0">勾股OA办公系统</td> 
</tr></table><hr style="border-color:#f1f1f1;margin:0">';
		$page= '<p style="text-align:center;font-size:12px; color:#999999">第 {PAGENO}/{nbpg} 页</p>';
		if($is_header){
			$mpdf->SetHTMLHeader($header);
		}
		if($is_page){
			$mpdf->SetHTMLFooter($page);
		}
        //以html为标准分析写入内容
        $mpdf->WriteHTML($html);
		//直接下载文件
		$mpdf->Output('tmp.pdf',true);
		$mpdf->Output($filename,"d");
		exit;
		/*
        //生成磁盘文件
        $mpdf->Output($path);
		if($type==0){
			//下载文件
			if (is_file($path)){
				//return to_assign(0,"文件生成成功");
				$file  =  fopen($path, "rb");
				Header( "Content-type:  application/octet-stream ");
				Header( "Accept-Ranges:  bytes ");
				Header( "Content-Disposition:  attachment;  filename= $filename");
				while (!feof($file)) {
					echo fread($file, 8192);
					ob_flush();
					flush();
				}
				fclose($file);
			} else {
				return to_assign(1,"文件生成失败");
			}
		}
		*/
    }
	
	public function excel()
    {
		$param = get_params();
        // 1. 查询数据（示例：从用户表查询）
        $users = Db::name('Admin')->field('a.id,a.name,a.mobile,d.title as department')
				->alias('a')
				->join('department d','a.did = d.id')
				->where([['a.status','=', 1],['a.id','>',1]])->select()->toArray();
		$detail = Db::name('Salary')->where('id',$param['id'])->find();
		$month = date('Y年m月',$detail['month_time']);
        // 2. 创建Spreadsheet对象
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 3. 【关键】设置默认样式为不锁定（所有单元格可编辑）
        $spreadsheet->getDefaultStyle()->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);

        // 4. 确保工作表保护未启用
        $sheet->getProtection()->setSheet(false);

		//头部样式
		$topStyle = [
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['argb' => 'FFFFE699'], // 灰色背景
			],
			'font' => [
				'color' => ['argb' => 'FFFF0000'], // 黑色字体
				'bold' => true, // 加粗
			],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical' => Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => 'FFBDD7EE'],
				],
			],
		];
		
		//表头样式
		$headerStyle = [
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['argb' => 'FFEEEEEE'], // 灰色背景
			],
			'font' => [
				'color' => ['argb' => 'FF000000'], // 黑色字体
				'bold' => true, // 加粗
			],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical' => Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '000000'],
				],
			],
		];
		
		//表格样式
		$bodyStyle = [
			'font' => [
				'color' => ['argb' => 'FF000000'], // 黑色字体
			],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical' => Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '000000'],
				],
			],
		];
		
		$footerStyle = [
			'font' => [
				'color' => ['argb' => 'FFFF0000'], // 黑色字体
				'bold' => true, // 加粗
			],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_LEFT,
				'vertical' => Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['argb' => '000000'],
				],
			],
		];
		
		$bgcolora = [
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['argb' => 'FFDDEBF7'], // 灰色背景
			]
		];
		$bgcolorb = [
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['argb' => 'FF2F75B5'], // 灰色背景
			],
			'font' => [
				'color' => ['argb' => 'FFFFFFFF'], // 黑色字体
				'bold' => true, // 加粗
			]
		];
		$bgcolorc = [
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['argb' => 'FFFFF2CC'], // 灰色背景
			]
		];
		$bgcolord = [
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['argb' => 'FFBF8F00'], // 灰色背景
			],
			'font' => [
				'color' => ['argb' => 'FFFFFFFF'], // 黑色字体
				'bold' => true, // 加粗
			]
		];
		$bgcolore = [
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['argb' => 'FFFFEBE7'], // 灰色背景
			]
		];
		$bgcolorf = [
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['argb' => 'FFC62D11'], // 灰色背景
			],
			'font' => [
				'color' => ['argb' => 'FFFFFFFF'], // 黑色字体
				'bold' => true, // 加粗
			]
		];
		$bgcolorg = [
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['argb' => 'FF12A003'], // 灰色背景
			],
			'font' => [
				'color' => ['argb' => 'FFFFFFFF'], // 黑色字体
				'bold' => true, // 加粗
			]
		];

		$sheet->mergeCells('A1:AB1');
		$sheet->setCellValue('A1', '每个表格的数据格式很重要，注意填写规范，数据无时，请填写0，数据请勿放在合并的单元格中');
		$sheet->getStyle('A1')->applyFromArray($topStyle);
		$sheet->getRowDimension(1)->setRowHeight(25);
        // 5. 设置表头
        $headers = ['系统ID号','员工姓名','手机号码','所在部门','工资月份','基本工资','岗位工资','绩效工资','全勤奖金','加班工资','用餐补贴','话费补贴','交通补贴','住房补贴','节假福利','保竞津贴','奖 金','应发合计','迟到扣除','事假扣除','旷工扣除','应扣合计','扣社保','扣公积金','代扣个税','扣款合计','实发合计','备注信息'];
        $columnCount = count($headers);
        
        for ($col = 0; $col < $columnCount; $col++) {
            $columnLetter = $this->numToLetter($col);
            $sheet->setCellValue($columnLetter . '2', $headers[$col]);
        }
		
		$sheet->getStyle('A2:AB2')->applyFromArray($headerStyle);
		$sheet->getStyle('F2:Q2')->applyFromArray($bgcolora);
		$sheet->getStyle('R2')->applyFromArray($bgcolorb);
		$sheet->getStyle('S2:U2')->applyFromArray($bgcolorc);
		$sheet->getStyle('V2')->applyFromArray($bgcolord);
		$sheet->getStyle('W2:Y2')->applyFromArray($bgcolore);
		$sheet->getStyle('Z2')->applyFromArray($bgcolorf);
		$sheet->getStyle('AA2')->applyFromArray($bgcolorg);
		$sheet->getRowDimension(2)->setRowHeight(25);
        // 6. 填充数据（从第2行开始）
        $row = 3;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user['id']);
            $sheet->setCellValue('B' . $row, $user['name']);
            $sheet->setCellValue('C' . $row, $user['mobile']);
            $sheet->setCellValue('D' . $row, $user['department']);
            $sheet->setCellValue('E' . $row, $month);
            $sheet->setCellValue('F' . $row, '');
            $sheet->setCellValue('G' . $row, '');
            $sheet->setCellValue('H' . $row, '');
            $sheet->setCellValue('I' . $row, '');
            $sheet->setCellValue('J' . $row, '');
            $sheet->setCellValue('K' . $row, '');
            $sheet->setCellValue('L' . $row, '');
            $sheet->setCellValue('M' . $row, '');
            $sheet->setCellValue('N' . $row, '');
            $sheet->setCellValue('O' . $row, '');
            $sheet->setCellValue('P' . $row, '');
            $sheet->setCellValue('Q' . $row, '');
            $sheet->setCellValue('R' . $row, '');
            $sheet->setCellValue('S' . $row, '');
            $sheet->setCellValue('T' . $row, '');
            $sheet->setCellValue('U' . $row, '');
            $sheet->setCellValue('V' . $row, '');
            $sheet->setCellValue('W' . $row, '');
            $sheet->setCellValue('X' . $row, '');
            $sheet->setCellValue('Y' . $row, '');
            $sheet->setCellValue('Z' . $row, '');
            $sheet->setCellValue('AA' . $row, '');
            $sheet->setCellValue('AB' . $row, '');
			
			//设置样式		
			$sheet->getStyle('A'.$row.':AB'.$row)->applyFromArray($bodyStyle);
			$sheet->getRowDimension($row)->setRowHeight(25);
            $row++;
        }

		$sheet->mergeCells('A'.$row.':AB'.$row);
		$sheet->setCellValue('A'.$row, '注意：系统ID号、员工姓名、手机号码、所在部门、工资月份内容无需修改，保持不变');
		$sheet->getStyle('A'.$row.':AB'.$row)->applyFromArray($footerStyle);
		$sheet->getRowDimension($row)->setRowHeight(25);		
        // 8. 设置列宽自适应
        for ($col = 0; $col < $columnCount; $col++) {
            $columnLetter = $this->numToLetter($col);
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }

        // 10. 输出到浏览器下载
        $filename = '工资模版_'.$detail['title'].'_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * 数字转Excel列字母（0->A, 1->B, 26->AA...）
     */
    private function numToLetter($num)
    {
        $letter = '';
        while ($num >= 0) {
            $letter = chr($num % 26 + 65) . $letter;
            $num = intdiv($num, 26) - 1;
        }
        return $letter;
    }
}
