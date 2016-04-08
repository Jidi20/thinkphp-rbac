<?php

/**
 * [db_create_in 字符串或数据转为sql IN形式]
 *
 * @param  [type] $item_list  [字符串或数组]
 * @param  string $field_name [字段名]
 *
 * @return [type]             [in()]
 */
function db_create_in($item_list, $field_name = '') {
    if (empty($item_list)) {
        return $field_name . " IN ('') ";
    } 
    else {
        if (!is_array($item_list)) {
            $item_list = explode(',', $item_list);
        }
        $item_list = array_unique($item_list);
        $item_list_tmp = '';
        foreach ($item_list AS $item) {
            if ($item !== '') {
                $item_list_tmp.= $item_list_tmp ? ",'$item'" : "'$item'";
            }
        }
        if (empty($item_list_tmp)) {
            return $field_name . " IN ('') ";
        } 
        else {
            return $field_name . ' IN (' . $item_list_tmp . ') ';
        }
    }
}

/**
 * [arrToTree 将数组转化为树形数组]
 *
 * @param  [type] $data [待处理的数组]
 * @param  [type] $pid  [父级ID号]
 *
 * @return [type]       [树形结构数组]
 */
function arrToTree($data, $pid) {
    if(empty($data))return array();
    $tree = array();
    foreach ($data as $k => $v) {
        if ($v['pid'] == $pid) {
            $v['pid'] = arrToTree($data, $v['id']);
            $tree[] = $v;
        }
    }
    return $tree;
}

/**
 * [genTree 扁平数据转为树]
 *
 * @param  [type] $items [扁平数据]
 * @param  string $id    [description]
 * @param  string $pid   [description]
 * @param  string $son   [description]
 *
 * @return [type]        [description]
 */
function genTree($items, $id = 'id', $pid = 'pid', $son = 'children') {
    if(empty($items))return array();
    $tree = array();
    
    //格式化的树
    $tmpMap = array();
    
    //临时扁平数据
    
    foreach ($items as $item) {
        $tmpMap[$item[$id]] = $item;
    }
    
    foreach ($items as $item) {
        if (isset($tmpMap[$item[$pid]])) {
            $tmpMap[$item[$pid]][$son][] = & $tmpMap[$item[$id]];
        } 
        else {
            $tree[] = & $tmpMap[$item[$id]];
        }
    }
    return $tree;
}

/**
 * [http_request http请求例程]
 *
 * @param  [type]  $url    [请求服务器url地址]
 * @param  integer $post   [post还是get]
 * @param  integer $return [1需要返回值，0不返回值]
 * @param  array   $data   [请求的数据]
 *
 * @return [type]          [description]
 */
function http_request($url, $post = 1, $return = 1, $data = array()) {
    if (empty($url)) {
        return;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, $return);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    if ($post == 1) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    $output = curl_exec($ch);
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return $error;
    }
    curl_close($ch);
    return $output;
}

/**
 * [changeArrKey 转换数组键值大小写]
 *
 * @param  [type]  $aValue [description]
 * @param  integer $case   [需要转换的数组]
 *
 * @return [type]          [0:小写、默认值；1：大写]
 */
function changeArrKey($aValue, $case = 0) {
    $aValueTemp = array();
    foreach ($aValue as $key => $item) {
        if ($case) {
            $keyTemp = strtoupper($key);
        } 
        else {
            $keyTemp = strtolower($key);
        }
        $aValueTemp[$keyTemp] = $item;
        if (is_array($item)) {
            $aValueTemp[$keyTemp] = changeArrKey($item, $case);
        }
    }
    return $aValueTemp;
}

/**
 * [array_change_value_case 将一维数组数组值转换大小写]
 *
 * @param  array  $a    [待转换的一维数组]
 * @param  [type] $case [大小写]
 *
 * @return [type]       [description]
 */
function array_change_value_case($a = array(), $case = CASE_LOWER) {
    if (empty($a)) return $a;
    if (!in_array($case, array(CASE_LOWER, CASE_UPPER))) $case = CASE_LOWER;
    if ($case == CASE_LOWER) {
        array_walk($a, function (&$v, $k) {
            $v = strtolower($v);
        });
    } 
    else {
        array_walk($a, function (&$v, $k) {
            $v = strtoupper($v);
        });
    }
    return $a;
}

/**
 * [array_change_value_case 将二维数组数组值转换大小写]
 *
 * @param  array  $a    [待转换的二维数组]
 * @param  [type] $case [大小写]
 *
 * @return [type]       [description]
 */
function array2_change_value_case($a = array(), $case = CASE_LOWER) {
    if (empty($a)) return $a;
    if (!in_array($case, array(CASE_LOWER, CASE_UPPER))) $case = CASE_LOWER;
    if ($case == CASE_LOWER) {
        array_walk($a, function (&$v, $k) {
            $v = array_change_value_case($v, CASE_LOWER);
        });
    } 
    else {
        array_walk($a, function (&$v, $k) {
            $v = array_change_value_case($v, CASE_UPPER);
        });
    }
    return $a;
}

/**
 * [array_ucfirst 将一维数组值转为首字母大写]
 *
 * @param  array  $a [待转换的数组]
 *
 * @return [type]    [description]
 */
function array_ucfirst($a = array()) {
    if (empty($a)) return $a;
    array_walk($a, function (&$v, $k) {
        $v = ucfirst(strtolower($v));
    });
    
    return $a;
}

/**
 * [array2_ucfirst 二维数组值转为首字母大写]
 *
 * @param  array  $a [待转换的二维数组]
 *
 * @return [type]    [description]
 */
function array2_ucfirst($a = array()) {
    if (empty($a)) return $a;
    array_walk($a, function (&$v, $k) {
        $v = array_ucfirst($v);
    });
    
    return $a;
}

/**
 * [in_array2 判断一个值是否在二维数组内]
 *
 * @param  [type] $val [给出的值]
 * @param  array  $a   [一维数组或二维数组]
 *
 * @return [type]      [description]
 */
function in_array2($val = null, $a = array()) {
    if (empty($a) || empty($val)) return false;
    if (!is_string($val)) return false;
    if (!is_array($a)) return false;
    if (in_array($val, $a)) return true;
    foreach ($a as $k => $v) {
        if (is_array($v)) {
            if (in_array($val, $v)) return true;
        } 
        else {
            if ($val == $v) return true;
        }
    }
    return false;
}

/** 判断某个值是否在二维数组的某个(键对应的)值中 */
function in_array2key($val, $arr, $key) {
	$ret = false;
	if($val != '' && !empty($arr) && is_array($arr) && $key != '' && is_string($key)) {
		foreach ($arr as $k => $v) {
			if(isset($v[$key])) {
				if($val == $v[$key]) {
					$ret = true;
					break;
				}
			} else {
				break;
			}
		}
	}
	return $ret;
}

/** 从二维数组中取出某个键的值组成的一维数组 */
function get1Dfrom2DByKey($arr, $key) {
	$ret = array();
	if(!empty($arr) && is_array($arr) && $key != '' && is_string($key)) {
		foreach ($arr as $k => $v) {
			if(isset($v[$key])) {
				$ret[] = $v[$key];
			} else {
				break;
			}
		}
	}
	return $ret;
}

/** 从二维数组中根据某个键的值获取其他键的值 */
function getValByKeyVal($arr, $key1, $val1, $key) {
	$ret = 0;
	if(!empty($arr) && is_array($arr) && !empty($key1) && !empty($key) && is_string($key1) && is_string($key)) {
		foreach ($arr as $k1 => $v1) {
			if(isset($v1[$key1])) {
				if($v1[$key1] == $val1) {
					$ret = $v1[$key];
					break;
				}
			} else {
				break;
			}
		}
	}
	return $ret;
}

/**
 * [copyright 版本号]
 *
 * @return [type] [description]
 */
function copyright() {
    $content = "Copyright &copy; " . date("Y") . "<a href='http://www.weijukeji.com' target='_blank'>微聚科技</a>";
    return $content;
}

/**
 * [gettree2 获得树形数组]
 *
 * @param  [type]  $arr [扁平数组]
 * @param  integer $pid [父级ID]
 *
 * @return [type]       [description]
 */
function gettree2($arr, $pid = 0) {
    $tree = array();
    foreach ($arr as $k => $v) {
        if ($v['pid'] == $pid) {
            $tree[] = $v;
        }
    }
    if (empty($tree)) {
        return null;
    }
    foreach ($tree as $k => $v) {
        $tree[$k]['son'] = gettree2($arr, $v['id']);
    }
    //print_r($tree);
    return $tree;
}

/**
 * [getxy 根据地址获取对应的坐标]
 *
 * @param  [type] $address [description]
 *
 * @return [type]          [description]
 */
function getxy($address) {
    $token_url2 = 'http://api.map.baidu.com/geocoder?address=' . $address . '&output=json&key=9923da4932b1a60e3383950ba2da4486';
    $access_token2 = file_get_contents($token_url2);
    $access_token2 = json_decode($access_token2, true);
    $xy = $access_token2['result']['location'];
    return $xy;
}

/**
 * [absimg 获取文件的绝对路径]
 *
 * @param  string $imgurl [文件的相对路径]
 *
 * @return [type]         [description]
 */
function absfileurl($fileurl = "") {
    if (empty($fileurl)) return;
    return $_SERVER['DOCUMENT_ROOT'] . $fileurl;
}

/**
 * 正则表达式验证email格式
 *
 * @param string $str    所要验证的邮箱地址
 * @return boolean
 */
function isEmail($str) {
    $isemail = filter_var($ip, FILTER_VALIDATE_EMAIL);
    if($isemail){
        return true;
    }else{
        return false;
    }
}

/**
 * 正则表达式验证网址
 *
 * @param string $str    所要验证的网址
 * @return boolean
 */
function isUrl($str) {
    if (!$str) {
        return false;
    }
    return preg_match('#(http|https|ftp|ftps)://([\w-]+\.)+[\w-]+(/[\w-./?%&=]*)?#i', $str) ? true : false;
}

/**
 * 验证字符串中是否含有汉字
 *
 * @param integer $string    所要验证的字符串。注：字符串编码仅支持UTF-8
 * @return boolean
 */
function isChineseCharacter($string) {
    if (!$string) {
        return false;
    }
    return preg_match('~[\x{4e00}-\x{9fa5}]+~u', $string) ? true : false;
}

/**
 * 验证字符串中是否含有非法字符
 *
 * @param string $string    待验证的字符串
 * @return boolean
 */
function isInvalidStr($string) {
    if (!$string) {
        return false;
    }
    return preg_match('#[!#$%^&*(){}~`"\';:?+=<>/\[\]]+#', $string) ? true : false;
}

/**
 * 用正则表达式验证邮证编码
 *
 * @param integer $num    所要验证的邮政编码
 * @return boolean
 */
function isPostNum($num) {
    if (!$num) {
        return false;
    }
    return preg_match('#^[1-9][0-9]{5}$#', $num) ? true : false;
}

/**
 * 正则表达式验证身份证号码
 *
 * @param integer $num    所要验证的身份证号码
 * @return boolean
 */
function isPersonalCard($num) {
    if (!$num) {
        return false;
    }
    return preg_match('#^[\d]{15}$|^[\d]{18}$#', $num) ? true : false;
}

/**
 * 正则表达式验证IP地址, 注:仅限IPv4
 *
 * @param string $str    所要验证的IP地址
 * @return boolean
 */
function isIp($str) {
    $isip = filter_var($ip, FILTER_VALIDATE_IP);
    if($isip){
        return true;
    }else{
        return false;
    }
}

/**
 * 用正则表达式验证出版物的ISBN号
 *
 * @param integer $str    所要验证的ISBN号,通常是由13位数字构成
 * @return boolean
 */
function isBookIsbn($str) {
    if (!$str) {
        return false;
    }
    return preg_match('#^978[\d]{10}$|^978-[\d]{10}$#', $str) ? true : false;
}

/**
 * 用正则表达式验证手机号码(中国大陆区)
 * @param integer $num    所要验证的手机号
 * @return boolean
 */
function isMobile($tel) {
    return preg_match("/^1[34578][0-9]{9}$/", $tel) ? true : false;
}

/**
 * 检查字符串是否为空
 *
 * @access public
 * @param string $string 字符串内容
 * @return boolean
 */
function isMust($string = null) {
    
    //参数分析
    if (is_null($string)) {
        return false;
    }
    return empty($string) ? false : true;
}

/**
 * 检查字符串长度
 *
 * @access public
 * @param string $string 字符串内容
 * @param integer $min 最小的字符串数
 * @param integer $max 最大的字符串数
 */
function isLength($string = null, $min = 0, $max = 255) {
    
    //参数分析
    if (is_null($string)) {
        return false;
    }
    
    //获取字符串长度
    $length = strlen(trim($string));
    return (($length >= (int)$min) && ($length <= (int)$max)) ? true : false;
}

/**
 * [padding 数据填充]
 *
 * @param  string  $str    [待填充的字符串]
 * @param  integer $length [填充后的长度]
 * @param  integer $padstr [用什么字符填充]
 * @param  [type]  $type   [填充类型]
 *
 * @return [type]          [description]
 */
function padding($str = "", $length = 0, $padstr = 0, $type = STR_PAD_LEFT) {
    if (empty($str)) return;
    if (!is_int($length) || $length < 1) $length = mb_strlen($str) + 1;
    if (!in_array($type, array(STR_PAD_LEFT, STR_PAD_RIGHT, STR_PAD_BOTH))) {
        $type = STR_PAD_LEFT;
    }
    return str_pad($str, $length, $padstr, $type);
}

/**
 * [str_insert 用字符在指定的位置插入到字符串中]
 *
 * @param  [type] $str        [被插入的字符串]
 * @param  [type] $i          [插入的位置]
 * @param  string $insert_str [指定用何字符串插入]
 *
 * @return [type]             [description]
 */
function str_insert($str, $i, $insert_str = "") {
    $str = strval($str);
    $substr = mb_substr($str, 0, $i, 'utf-8');
    $laststr = mb_substr($str, $i, mb_strlen($str, "utf-8") - $i, 'utf-8');
    $substr.= $insert_str . $laststr;
    return $substr;
}

/**
 * [createOrder_sn 生成订单号]
 *
 * @return [type] [description]
 */
function createOrder_sn() {
    return NOW_TIME . rand(10000, 99999);
}

/**
 * [msubstr 截取字符串]
 *
 * @param  [type]  $str     [原始字符串]
 * @param  integer $start   [从何处开始截取]
 * @param  [type]  $length  [截取字符串的长度]
 * @param  string  $charset [字符编码]
 * @param  boolean $suffix  [截取后是否需要有...代替被截掉的字符]
 *
 * @return [type]           [description]
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
    if (function_exists("mb_substr")) {
        $slice = mb_substr($str, $start, $length, $charset);
    } 
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
    } 
    else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    $chaochu = true;
    if (function_exists("mb_strlen")) {
        $len = mb_strlen($str, $charset);
        if ($len <= $length) $chaochu = false;
    }
    
    return $suffix ? ($chaochu ? $slice . '...' : $slice) : $slice;
}

/**
 * [getweek 根据php的星期数字获取中文星期]
 *
 * @param  integer $week [数字星期]
 *
 * @return [type]        [description]
 */
function getweek($week = 0) {
    switch ($week) {
        case 1:
            $w = "星期一";
            break;

        case 2:
            $w = "星期二";
            break;

        case 3:
            $w = "星期三";
            break;

        case 4:
            $w = "星期四";
            break;

        case 5:
            $w = "星期五";
            break;

        case 6:
            $w = "星期六";
            break;

        default:
            $w = "星期日";
            break;
    }
    return $w;
}

/**
 * 获取指定日期对应星座
 *
 * @param integer $month 月份 1-12
 * @param integer $day 日期 1-31
 * @return boolean|string
 */
function getConstellation($month, $day) {
    $day = intval($day);
    $month = intval($month);
    if ($month < 1 || $month > 12 || $day < 1 || $day > 31) return false;
    $signs = array(array('20' => '宝瓶座'), array('19' => '双鱼座'), array('21' => '白羊座'), array('20' => '金牛座'), array('21' => '双子座'), array('22' => '巨蟹座'), array('23' => '狮子座'), array('23' => '处女座'), array('23' => '天秤座'), array('24' => '天蝎座'), array('22' => '射手座'), array('22' => '摩羯座'));
    list($start, $name) = each($signs[$month - 1]);
    if ($day < $start) list($start, $name) = each($signs[($month - 2 < 0) ? 11 : $month - 2]);
    return $name;
}

/**
 * [maxMin 获取数组中最值]
 *
 * @param  array   $arr [数组]
 * @param  boolean $max [true：最大值，false：最小值]
 *
 * @return [type]       [description]
 */
function maxMin($arr = array(), $max = true) {
    $arr = array_filter($arr, function ($v) {
        return empty($v) ? false : true;
    });
    if (empty($arr)) return;
    $cmpTime = 0;
    $count = count($arr);
    $biggest = $smallest = $arr[$count - 1];
    
    //每次取出两个元素，比较两个元素的大小再与最大值和最小值比较
    for ($i = 0; $i < $count - 1; $i+= 2) {
        $cmpTime++;
        if ($arr[$i] > $arr[$i + 1]) {
            $bigger = $arr[$i];
            $smaller = $arr[$i + 1];
        } 
        else {
            $bigger = $arr[$i + 1];
            $smaller = $arr[$i];
        }
        $cmpTime++;
        if ($bigger > $biggest) {
            $biggest = $bigger;
        }
        $cmpTime++;
        if ($smaller < $smallest) {
            $smallest = $smaller;
        }
    }
    if ($max) return $biggest;
    else return $smallest;
}

/**
 * 内容加密解密方法
 * $string = 加密的内容 （不可为空）
 * $operation = 加密/解密类型 （D 为解密，非D 为加密）
 * $key = 加密和解密的副加值（可为空）
 * $stk = 将网址上容易出错的"/"斜杠换成"*"（可为空）
 */
function encrypt($string, $operation = 'E', $key = 'weiju', $stk = "*") {
    $operation = strtoupper($operation);
    $key = md5($key);
    $key_length = strlen($key);
    if ($operation == 'D') {
        $string = base64_decode(str_replace($stk, '/', $string));
    } 
    else {
        $string = substr(md5($string . $key), 0, 8) . $string;
    }
    $string_length = strlen($string);
    $rndkey = $box = array();
    $result = '';
    for ($i = 0; $i <= 256; $i++) {
        $rndkey[$i] = ord($key[$i % $key_length]);
        $box[$i] = $i;
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 255;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result.= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 255]));
    }
    if ($operation == 'D') {
        if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
            return substr($result, 8);
        } 
        else {
            return '';
        }
    } 
    else {
        return str_replace('=', '', str_replace('/', $stk, base64_encode($result)));
    }
}

/**
 * [isEmpty 判断内容是否为空]
 *
 * @param  [type]  $content [原始内容]
 * @param  string  $type    [description]
 *
 * @return boolean          [description]
 */
function isEmpty($content, $type = "content") {
    if (empty($content)) return "--";
    return $content = $type == "time" ? date("Y/m/d H:i:s", $content) : $content;
}

/**
 * [base64img 将图片转为base64格式]
 *
 * @param  [type] $file [图片地址]
 *
 * @return [type]       [description]
 */
function base64img($file = null) {
    if (!$file) return;
    $file = $_SERVER['DOCUMENT_ROOT'] . $file;
    $type = getimagesize($file);
    
    //取得图片的大小，类型等
    $fp = fopen($file, "r");
    if (!$fp) return "Can't open file";
    $file_content = chunk_split(base64_encode(fread($fp, filesize($file))));
    
    //base64编码
    switch ($type[2]) {
            
            //判读图片类型
            
        case 1:
            $img_type = "gif";
            break;

        case 2:
            $img_type = "jpg";
            break;

        case 3:
            $img_type = "png";
            break;
    }
    $img = 'data:image/' . $img_type . ';base64,' . $file_content;
    
    //合成图片的base64编码
    fclose($fp);
    return str_replace(PHP_EOL, "", $img);
}

/**
 * [multi_array_sort 多维数组排序]
 *
 * @param  [type]  $multi_array [多维数组]
 * @param  [type]  $sort_key    [排序需要参照的键名]
 * @param  [type]  $sort        [SORT_ASC:顺序，SORT_DESC：倒序]
 * @param  integer $num         [截取前几条数据，0:不截取]
 *
 * @return [type]               [description]
 */
function multi_array_sort($multi_array, $sort_key, $sort = SORT_ASC, $num = 0) {
    if (is_array($multi_array)) {
        foreach ($multi_array as $row_array) {
            if (is_array($row_array)) {
                $key_array[] = $row_array[$sort_key];
            } 
            else {
                return false;
            }
        }
    } 
    else {
        return false;
    }
    array_multisort($key_array, $sort, $multi_array);
    if ($num > 0) {
        return array_slice($multi_array, 0, $num);
    } 
    else {
        return $multi_array;
    }
}

/**
 * [gethostwithhttp 获取当前服务器域名(带http)]
 *
 * @return [type] [description]
 */
function gethostwithhttp() {
    $ret = '';
    $domain = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
    if ($domain) {
        $ret = (is_ssl() ? 'https://' : 'http://') . $domain;
    }
    
    return $ret;
}

/** 
 * [getrandstr 产生随机字符串，参数是字符长度,是否允许大写字母]
 *
 * @param  [type]  $length [字符长度]
 * @param  boolean $upper  [大小写]
 *
 * @return [type]          [description]
 */
function getrandstr($length, $upper = false) {
    $chars = $upper ? "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789" : "abcdefghijklmnopqrstuvwxyz0123456789";
    $str = "";
    if (is_numeric($length) && $length > 0) {
        for ($i = 0; $i < $length; $i++) {
            $str.= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
    }
    
    return $str;
}

/**
 *  @desc 根据两点间的经纬度计算距离
 *  @param float $lat 纬度值
 *  @param float $lng 经度值
 *  @param $status true KM，M显示;false 只返回M
 */
function getDistance($lat1, $lng1, $lat2, $lng2, $status = true) {
    $earthRadius = 6367000;
    
    //approximate radius of earth in meters
    $lat1 = ($lat1 * pi()) / 180;
    $lng1 = ($lng1 * pi()) / 180;
    $lat2 = ($lat2 * pi()) / 180;
    $lng2 = ($lng2 * pi()) / 180;
    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;
    if ($status) {
        $m = round($calculatedDistance) / 1000;
        return $m > 1 ? round($m, 1) . "km" : ($m * 1000) . "m";
    } 
    else {
        return round($calculatedDistance) . "m";
    }
}

/**
 * [generate_salt 生成盐或密码]
 *
 * @param  string $type   [类型]
 * @param  string $number [生成字符的个数]
 *
 * @return [type]         [description]
 */
function generate_salt($type = '6', $number = '5') {
    
    //全小写字母
    $salttype['chars0'] = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    
    //全大写字母
    $salttype['chars1'] = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    
    //全数字
    $salttype['chars2'] = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    
    //小写字母+大写字母
    $salttype['chars3'] = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    
    //小写字母+数字
    $salttype['chars4'] = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    
    //大写字母+数字
    $salttype['chars5'] = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    
    //小写字母+大写字母+数字
    $salttype['chars6'] = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $chars = $salttype['chars' . $type];
    $keys = array_rand($chars, $number);
    foreach ($keys as $v) {
        $ints.= $chars[$v];
    }
    return $ints;
}

/**
 * [modeofpayment 本系统默认的收款方式（目前只有10种）]
 *
 * @return [type] [description]
 */
function modeofpayment() {
    $payment = array(array('shop_id' => $this->shop_id, 'name' => '现金', 'type' => '0', 'is_auth' => '0', 'is_writeno' => '0', 'system_name' => '现金', 'system_name2' => 'cash'), array('shop_id' => $this->shop_id, 'name' => '刷卡', 'type' => '0', 'is_auth' => '0', 'is_writeno' => '0', 'system_name' => '刷卡', 'system_name2' => 'bank_money'), array('shop_id' => $this->shop_id, 'name' => '会员卡', 'type' => '0', 'is_auth' => '0', 'is_writeno' => '0', 'system_name' => '会员卡', 'system_name2' => 'card_money'), array('shop_id' => $this->shop_id, 'name' => '优惠劵', 'type' => '0', 'is_auth' => '0', 'is_writeno' => '1', 'system_name' => '优惠劵', 'system_name2' => 'youhui_money'), array('shop_id' => $this->shop_id, 'name' => '单位挂账', 'type' => '0', 'is_auth' => '1', 'is_writeno' => '0', 'system_name' => '单位挂账', 'system_name2' => 'ledger_money'), array('shop_id' => $this->shop_id, 'name' => '', 'type' => '1', 'is_auth' => '1', 'is_writeno' => '1', 'system_name' => '系统内置1', 'system_name2' => 'other'), array('shop_id' => $this->shop_id, 'name' => '', 'type' => '1', 'is_auth' => '1', 'is_writeno' => '1', 'system_name' => '系统内置2', 'system_name2' => 'other1'), array('shop_id' => $this->shop_id, 'name' => '', 'type' => '1', 'is_auth' => '1', 'is_writeno' => '1', 'system_name' => '系统内置3', 'system_name2' => 'other2'), array('shop_id' => $this->shop_id, 'name' => '', 'type' => '1', 'is_auth' => '1', 'is_writeno' => '1', 'system_name' => '系统内置4', 'system_name2' => 'other3'), array('shop_id' => $this->shop_id, 'name' => '', 'type' => '1', 'is_auth' => '1', 'is_writeno' => '1', 'system_name' => '系统内置5', 'system_name2' => 'other4'));
    return $payment;
}

/** 微信回复 文本模板 */
function transmitText($object, $content, $flag = 0) {
    $textTpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                <FuncFlag>%d</FuncFlag>
                </xml>";
    $resultStr = sprintf($textTpl,$object->FromUserName,$object->ToUserName, NOW_TIME, $content, $flag);
    return $resultStr;
}


/** 微信回复 图文模板 */
function transmitNews($object, $content,$flag = 0) {
    $int = count($content);
    $textTpl .= "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>".$int."</ArticleCount>
                <Articles>";

                foreach($content as $v){
                    $textTpl .= "<item>
                        <Title><![CDATA[".$v['title']."]]></Title>
                        <Description><![CDATA[".$v['description']."]]></Description>
                        <PicUrl><![CDATA[".$v['PicUrl']."]]></PicUrl>
                        <Url><![CDATA[".$v['Url']."]]></Url>
                    </item>";
                }

    $textTpl .= "</Articles><FuncFlag>%d</FuncFlag></xml> ";

    $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, NOW_TIME, $content, $flag);
    return $resultStr;
}

    /**
     * 无线级分类
     * 将分类分组组合；
     * 应用举例：后台某个分组下面显示其子级分组;
     * @param  [type]  $list  [所有分类]
     * @param  string  $html  [显示间隔]
     * @param  integer $pid   [默认的顶级分类]
     * @param  integer $level [分类所属级别]
     * @return [type]         [数组]
     */
    function unlimitedForLevel($list,$html='',$pid=0,$level=0){
        $arr = array();
        foreach ($list as $v) {
            if($v['pid'] == $pid){
                $v['level'] = $level+1;
                $v['html'] = str_repeat($html, $level);
                $arr[] = $v;
                $arr = array_merge($arr,unlimitedForlevel($list,$html,$v['id'],$level+1));
            }
        }
        return $arr;
    }

    /**
     * 获得父级下面的所有子级
     * 应用举例：做分类导航；例：PHP->数组->数组函数
     * @param  [type]  $list [所有分类]
     * @param  integer $pid  [父级ID]
     * @return [type]        [返回父级ID下的所有子级信息]
     */
    function unlimitedForLayer($list,$pid=0){
        $arr = array();
        foreach ($list as $v) {
            if($v['pid'] == $pid){
                $v['child'] = unlimitedForLayer($list,$v['id']);
                $arr[] = $v;
            }
        }
        return $arr;
    }

    /**
     * 传递当前分类ID，获得其父ID；
     * 应用：面包屑导航；
     * @param  [type] $list [所有分类]
     * @param  [type] $id   [当前ID]
     * @return [type]       [返回当前分类ID所有父级]
     */
    function getParents($list,$id){
        $arr = array();
        foreach ($list as $v) {
            if($v['id'] == $id){
                $arr[] = $v;
                $arr = array_merge($arr,getParents($list,$v['pid']));
            }
        }
        return $arr;
    }

    /**
     * 传递父级ID，获得其所有子级ID
     * 应用举例：根据所有子级ID，来获得该父级下的所有商品等；
     * @param  [type] $list [所有分类]
     * @param  [type] $pid  [父级ID]
     * @return [type]       [返回所有子级ID]
     */
    function getChildsID($list,$pid){
        $arr = array();
        foreach($list as $v){
            if($v['pid'] == $pid){
                $arr[] = $v['id'];
                $arr = array_merge($arr,getChildsID($list,$v['id']));
            }
        }
        return $arr;
    }

    /**
     * 传递父级ID，获得父级ID下所有的子级
     * @param  [type] $list [所有分类]
     * @param  [type] $pid  [父级ID]
     * @return [type]       [返回父级ID下的所有子级]
     */
    function getChilds($list,$pid){
        $arr = array();
        foreach ($list as  $v) {
            if($v['pid'] == $pid){
                $arr[] = $v;
                $arr = array_merge($arr,getChilds($list,$v['id']));
            }
        }
        return $arr;
    }
    /**
     * [删除图片]
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    function deleteImg($url){
        $img_url = $_SERVER['DOCUMENT_ROOT'].$url;
        $url = str_replace('\\','/',$img_url);
        @unlink($url);
    }
    /**
     * ['\'=>'/'] 把路径中的 \ 换成 /
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    function slashto($url){
        $url = str_replace('\\','/',$url);
        return $url;
    }
    
/** 生成原生insert语句,表名不要前缀 */
function getInsertSql($table, $setArr) {
	$ret = '';
	if(!empty($table) && is_string($table) && !empty($setArr) && is_array($setArr)) {
		$table = C('DB_PREFIX').$table;
		$condition = array();
		
		foreach ($setArr as $key=>$val)
		{
			$condition[] = "`{$key}`='{$val}'";
		}
		
		$setStr = implode(',', $condition);
		$ret = "INSERT INTO `{$table}` SET {$setStr} ";
	}
	
	return $ret;
}

/** 生成原生update语句,表名不要前缀 */
function getUpdateSql($table, $setArr, $whereArr) {
	$ret = '';
	if(!empty($table) && is_string($table) && !empty($setArr) && is_array($setArr) && !empty($whereArr) && is_array($whereArr)) {
		$table = C('DB_PREFIX').$table;
		$sc = array();
		foreach($setArr as $keys => $vals)
		{
			$sc[] = "`{$keys}`='{$vals}'";
		}
		$setCond = implode(',', $sc);
		
		$wc = array();
		foreach($whereArr as $keyw => $valw)
		{
			$wc[] = "`{$keyw}`='{$valw}'";
		}
		$whereCond = implode(' AND ', $wc);
		
		$ret = "UPDATE `{$table}` SET {$setCond} WHERE {$whereCond}  ";
	}
	
	return $ret;
}

/** https请求（支持GET和POST） */
function https_request($url, $data = null) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	if (!empty($data)) {
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	}
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}

/** 把微信性别转换成微餐族使用的性别 */
function getSexFromWx2Wcz($sex) {
	$ret = 2; //保密
	if(1 == $sex) { //男
		$ret = 0;
	} else if(2 == $sex){ //女
		$ret = 1;
	}
	return $ret;
}

?>
