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
namespace app\honor\controller;

use app\api\BaseController;
use think\facade\Db;

class Api extends BaseController
{
    /**
     * 证书下载
     */

    public function certificate_down()
    {
        $param = get_params();
        $id = $param['id'];
		
		$detail = Db::name('CertificateUsers')->where(['id' => $id])->find();
		
		$certificate = Db::name('Certificate')->where(['id' => $detail['certificate_id']])->find();
		$user = Db::name('Admin')->where(['id' => $detail['user_id']])->find();
		
		$code = $certificate['code'] . $detail['certificate_id'] . $detail['id'];
        $default_name = $code . ".png";
        $link_url = "/storage/avatars/certificate_" . $default_name;
        $real_url = CMS_ROOT . "public" . $link_url;
		
        $bg_path = CMS_ROOT . "public/static/home/images/zhengshu" . $certificate['type'] . ".jpg"; //背景图片路径

		//创建图片的实例
        $bg = imagecreatefromstring(file_get_contents($bg_path));

        //字体文件
        $font_file = CMS_ROOT . "public/static/home/mpdf/MiSans-Regular.ttf";
        $font_file_bold = CMS_ROOT . "public/static/home/mpdf/MiSans-Bold.ttf";
		
        //设定字体的颜色
        $font_color_black = ImageColorAllocate($bg, 51, 51, 51);
        $font_color_gray = ImageColorAllocate($bg, 120, 120, 120);
        $font_color_blue = ImageColorAllocate($bg, 32, 39, 92);
        $font_color_red = ImageColorAllocate($bg, 192, 69, 59);
		
		
        //y位置
        $position = [
            [0,0,0,0,0,0],
            [300, 520,660,1072,1130,1240,$font_color_black],
            [300, 550,660,1072,1130,1206,$font_color_blue],
            [300, 0,560,1000,1060,1160,$font_color_red],
        ];
		
		$py = $position[$certificate['type']];
		
		//绘制证书标题
		$nameBox = imagettfbbox(58, 0, $font_file_bold, $certificate['name']);//获取文字所需的尺寸大小
		$x_name = (1000 - $nameBox[2]) / 2;
        imagettftext($bg, 58, 0,(int)$x_name, $py[0], $py[6], $font_file_bold, $certificate['name']);

        //绘制姓名
		if($certificate['type']<3){
			$nameBox = imagettfbbox(36, 0, $font_file_bold, $user['name']);//获取文字所需的尺寸大小
			$x_user = (1000 - $nameBox[2]) / 2;
			imagettftext($bg, 36, 0, (int)$x_user, $py[1], $font_color_black, $font_file_bold, $user['name']);
		}
        //绘制描述
		if($certificate['type'] == 3){
			$certificate['content_top'] = $user['name'].'，'.$certificate['content_top'];
		}
        $theTitle = $this->cn_row_substr($certificate['content_top'], 2, 22);
        $y = $py[2];
        foreach ($theTitle as &$v) {
            imagettftext($bg, 27, 0, 96, $y, $font_color_black, $font_file_bold, $v);
            $y = $y + 80;
        }
        imagettftext($bg, 27, 0, 96, $y + 100, $font_color_black, $font_file_bold, $certificate['content_bottom']);
		
		//绘制签发机构
		imagettftext($bg, 18, 0, 96, $py[3], $font_color_black, $font_file, '签发机构：'.$certificate['signer']);
		//绘制签发时间
		imagettftext($bg, 18, 0, 96, $py[4], $font_color_black, $font_file, '签发日期：'.date('Y年m月d日', $detail['create_time']));
		
		//绘制证书编号
		imagettftext($bg, 12, 0, 96, $py[5], $font_color_gray, $font_file, '证书编号：'.$code);
		
        //输出图片
        if ($real_url) {
            imagepng($bg, $real_url);
        } else {
            header('Content-Type: image/png');
            imagepng($bg);
        }
        //释放空间
        imagedestroy($bg);
		// 设置300秒有效期
		return download($real_url,$user['name'].'_'.$code.'.png')->expire(300);
        //$this->apiSuccess($real_url);
    }
	
/**
 * 分行连续截取字符串
 * @param $str  需要截取的字符串,UTF-8
 * @param int $row  截取的行数
 * @param int $number   每行截取的字数，中文长度
 * @param bool $suffix  最后行是否添加‘...’后缀
 * @return array    返回数组共$row个元素，下标1到$row
 */

    public function cn_row_substr($str, $row = 1, $number = 10, $suffix = true)
    {
        $result = array();
        for ($r = 1; $r <= $row; $r++) {
            $result[$r] = '';
        }

        $str = trim($str);
        if (!$str) {
            return $result;
        }

        $theStrlen = strlen($str);

        //每行实际字节长度
        $oneRowNum = $number * 3;
        for ($r = 1; $r <= $row; $r++) {
            if ($r == $row and $theStrlen > $r * $oneRowNum and $suffix) {
                $result[$r] = $this->mg_cn_substr($str, $oneRowNum - 6, ($r - 1) * $oneRowNum) . '...';
            } else {
                $result[$r] = $this->mg_cn_substr($str, $oneRowNum, ($r - 1) * $oneRowNum);
            }
            if ($theStrlen < $r * $oneRowNum) {
                break;
            }

        }
        return $result;
    }

/**
 * 按字节截取utf-8字符串
 * 识别汉字全角符号，全角中文3个字节，半角英文1个字节
 * @param $str  需要切取的字符串
 * @param $len  截取长度[字节]
 * @param int $start    截取开始位置，默认0
 * @return string
 */

    public function mg_cn_substr($str, $len, $start = 0)
    {
        $q_str = '';
        $q_strlen = ($start + $len) > strlen($str) ? strlen($str) : ($start + $len);

        //如果start不为起始位置，若起始位置为乱码就按照UTF-8编码获取新start
        if ($start and json_encode(substr($str, $start, 1)) === false) {
            for ($a = 0; $a < 3; $a++) {
                $new_start = $start + $a;
                $m_str = substr($str, $new_start, 3);
                if (json_encode($m_str) !== false) {
                    $start = $new_start;
                    break;
                }
            }
        }

        //切取内容
        for ($i = $start; $i < $q_strlen; $i++) {
            //ord()函数取得substr()的第一个字符的ASCII码，如果大于0xa0的话则是中文字符
            if (ord(substr($str, $i, 1)) > 0xa0) {
                $q_str .= substr($str, $i, 3);
                $i += 2;
            } else {
                $q_str .= substr($str, $i, 1);
            }
        }
        return $q_str;
    }
}
