<?php
namespace Think;
/**
 * 微信jssdk控制器
 */
class JssdkController extends Controller{
  private $appId;
  private $appSecret;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $NOW_TIME = NOW_TIME;
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$NOW_TIME&url=$url";
    $signature = sha1($string);

	  $partner = 10023296; //微信支付商户号
    $body = '这是商品描述，很好的商品哦，快来买吧';
    $notify_url = 'http://www.baidu.com';
    $out_trade_no = date('YmdHis').mt_rand(10000, 99999);
    $cip = CLIENT_IP;
	  $total_fee = 0.01;
    $paypag = "bank_type=WX&body={$body}&fee_type=1&input_charset=UTF-8&notify_url={$notify_url}&out_trade_no={$out_trade_no}&partner={$partner}&spbill_create_ip={$cip}&total_fee={$total_fee}";

	  $appid = $this->appId;
	  $stringA = "appid={$appid}&body=testbody&device_info=1000&mch_id={$partner}&nonce_str={$nonceStr}";
	  $stringSignTemp = "{$stringA}&key=192006250b4c09247ec02edce69f6a2d";
	  $paySign = strtoupper(md5($stringSignTemp));

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "NOW_TIME" => $NOW_TIME,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string,
	    "paypag"    => $paypag,  //addition=action_id%3dgaby1234%26limit_pay%3d&bank_type=WX&body=innertest&fee_type=1&input_charset=GBK&notify_url=http%3A%2F%2F120.204.206.246%2Fcgi-bin%2Fmmsupport-bin%2Fnotifypay&out_trade_no=1414723227818375338&partner=1900000109&spbill_create_ip=127.0.0.1&total_fee=1&sign=432B647FE95C7BF73BCD177CEECBEF8D
	    "paySign"   => $paySign,
    );
    return $signPackage;
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  /** 获取微信jsapi_ticket */
  private function getJsApiTicket() {
  	$ticket = '';
  	$wxapi = new \Think\WeixinapiController();
  	$ticket = $wxapi->getWxPubJsapiTicket(array('appId'=>$this->appId, 'appSecret'=>$this->appSecret));

    return $ticket;
  }
  
  /**
   * [getAccessToken 生成微信access_token]
   * @return [type] [description]
   */
  private function getAccessToken() {
    $access_token = '';
    
    $wxapi = new \Think\WeixinapiController();
    $access_token = $wxapi->getWxPubAccessToken(array('appId'=>$this->appId, 'appSecret'=>$this->appSecret));
    
    return $access_token;
  }


  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }


  //网页授权，获取用户资料
  /* 已有openid */
 /* public function wei_user_content($openid,$size="0"){
		//获取微信所给出的access_token
		$access_token = $this->getAccessToken();
		//获取微信所返回的用户信息
		$user_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid;
		$wei_user = file_get_contents($user_url);
		$wei_user = json_decode($wei_user , true);
		//是否获取到用户信息
		if(isset($wei_user['openid'])){
			//处理头像地址,更改需要头像的尺寸
			if($wei_user['headimgurl']!=""){
				$new_headimgurl=substr($wei_user['headimgurl'],0,strripos($wei_user['headimgurl'],'/'));
				$wei_user['headimgurl']=$new_headimgurl.'/'.$size;
			}
			return $wei_user;
		}else{
			return false;
		}
	}*/

	/* 用户同意授权，获取临时的 code 值  */
/*	public function wei_code_url($returnurl,$type=true){
		$appid = $this->appId;
		$scope=$type?"snsapi_userinfo":"snsapi_base";
		//使用urlencode对链接进行处理
		$returnurl=urlencode($returnurl);
		$weixinurl="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$returnurl."&response_type=code&scope=".$scope."&state=STATE#wechat_redirect";
		//echo $weixinurl;exit;
		return $weixinurl;
	}*/

	/* 通过code换取网页授权access_token 返回openID 并获取用户信息 */
/*	public function wei_accesstoken_user($code,$size="0",$type=true){
		$appid = $this->appId;
		$secret = $this->appSecret;
		$get_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
		$get_return = file_get_contents($get_url);
		$get_return = json_decode($get_return , true);
		if($type){
			return $this->wei_get_user_content($get_return['openid'],$get_return['access_token'],$size);
		}else{
			return $get_return;
		}
	}*/

	/* 拉取用户信息(需授权的用户) */
/*	public function wei_get_user_content($openid,$accesstoken,$size="0"){
		$get_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$accesstoken.'&openid='.$openid.'&lang=zh_CN';
		$get_return = file_get_contents($get_url);
		$get_return = json_decode($get_return , true);
		if(isset($get_return['openid'])){
			//处理头像地址,更改需要头像的尺寸
			if($get_return['headimgurl']!=""){
				$new_headimgurl=substr($get_return['headimgurl'],0,strripos($get_return['headimgurl'],'/'));
				$get_return['headimgurl']=$new_headimgurl.'/'.$size;
			}
			return $get_return;
		}else{
			return false;
		}
	}*/

	/** 保存微信服务器上图片(多媒体文件) */
	public function getwximg($media_id) {
		global $_W;

		$media_id = trim($media_id);
		$ret = '';
		$accessToken = $this->getAccessToken();
		//$accessToken = account_weixin_token($_W['account']);
		if('' != $media_id) {
			$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$accessToken}&media_id={$media_id}";

			$cont = $this->httpGet($url);
			$filename = "wximg/".md5($media_id).".jpg";
			$rf = file_write($filename, $cont);
			$ret = $rf ? $filename : 'img upload error';
		} else {
			$ret = "accessToken== $accessToken   --- media_id = $media_id";
		}
			//$this->logging_write("ret==".$ret);
		return $ret;
	}

	/** 保存微信服务器上音频(多媒体文件) */
	public function getwxaudio($media_id) {
		global $_W;

		$media_id = trim($media_id);
		$ret = '';
		$accessToken = $this->getAccessToken();
		//$accessToken = account_weixin_token($_W['account']);
		if('' != $media_id) {
			$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$accessToken}&media_id={$media_id}";

			$cont = $this->httpGet($url);
			$filename = "wxaudio/".md5($media_id).".mp3";
			$rf = file_write($filename, $cont);
			$ret = $rf ? $filename : 'audio upload error';
		} else {
			$ret = "accessToken== $accessToken   --- media_id = $media_id";
		}
			//$this->logging_write("ret==".$ret);
		return $ret;
	}

	/** 日志写入方法 */
	/*public function logging_write($msg, $type='debug') {
		$msg = serialize($msg);
		$path = IA_ROOT . '/data/logs/';
		$logFile = $path.$type.'_'.date('Ymd').'.log';
		$now = date('Y-m-d H:i:s');
		$msg = "[{$now}] {$msg} \n";
		error_log($msg, 3, $logFile);
	}*/
}


