<?php
namespace Think;
/**
 * 微信api控制器
 */
class WeixinapiController extends Controller{
	public $logger; //日志对象
	
	function __construct() {
		import("Org.Util.Logger");
		$this->logger = new \Org\Util\Logger();
	}
	
	/** 获取某微信公众号的access_token(公众号全局唯一，有效期2小时) */
	public function getWxPubAccessToken($args) {
		$ret = '';
		if(!empty($args) && is_array($args)) {
			
			$where = '';
			if(isset($args['id']) && !empty($args['id'])) {
				$where = "id={$args['id']}";
			} else if(isset($args['hash']) && !empty($args['hash'])) {
				$where = "hash='{$args['hash']}'";
			} else if(isset($args['account']) && !empty($args['account'])) {
				$where = "account='{$args['account']}'";
			} else if(isset($args['original']) && !empty($args['original'])) {
				$where = "original='{$args['original']}'";
			} else if(isset($args['username']) && !empty($args['username'])) {
				$where = "username='{$args['username']}'";
			} else if(isset($args['appId']) && !empty($args['appId'])) {
				$where = "appId='{$args['appId']}'";
			} else if(isset($args['appSecret']) && !empty($args['appSecret'])) {
				$where = "appSecret='{$args['appSecret']}'";
			}
			
			if(!empty($where) && is_string($where)) {
				$wechatsinfo = D('wx_wechats')->where($where)->find();
				//$this->logger->debug(D('wechats')->_sql());
				if(!empty($wechatsinfo) && is_array($wechatsinfo)) {
				
					$arrat = json_decode($wechatsinfo['access_token'], true);
					if(!empty($arrat) && is_array($arrat) && $arrat['expire_time'] > NOW_TIME) {
						$ret = $arrat['access_token'];
					} else {
						$token_url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$wechatsinfo['appId'].'&secret='.$wechatsinfo['appSecret'];
						$access_token = file_get_contents($token_url);
						$access_token = json_decode($access_token , true);
						if ($access_token['access_token']) {
							$data['access_token'] = $access_token['access_token'];
							$data['expire_time']  = NOW_TIME + 7000;
			
							$json_data = json_encode($data);
	
							D('wechats')->where($where)->save(array('access_token'=>$json_data));
							$ret = $access_token['access_token'];
							//$this->logger->debug(array(2222, $ret));
						} else {
							$this->logger->error(array(__METHOD__, '未从微信官方接口获得access_token数据', func_get_args()));
						}
					}
				} else {
					$this->logger->error(array(__METHOD__, 'DB中无此微信公众号的数据', func_get_args()));
				}
			} else {
				$this->logger->error(array(__METHOD__, '参数缺失或错误', func_get_args()));
			}
		}
		return $ret;
	}
	
	/** 获取某微信公众号的jsapi_ticket(公众号全局唯一，有效期2小时) */
	public function getWxPubJsapiTicket($args) {
		$ret = '';
		if(!empty($args) && is_array($args)) {
			
			$where = '';
			if(isset($args['id']) && !empty($args['id'])) {
				$where = "id={$args['id']}";
			} else if(isset($args['hash']) && !empty($args['hash'])) {
				$where = "hash='{$args['hash']}'";
			} else if(isset($args['account']) && !empty($args['account'])) {
				$where = "account='{$args['account']}'";
			} else if(isset($args['original']) && !empty($args['original'])) {
				$where = "original='{$args['original']}'";
			} else if(isset($args['username']) && !empty($args['username'])) {
				$where = "username='{$args['username']}'";
			} else if(isset($args['appId']) && !empty($args['appId'])) {
				$where = "appId='{$args['appId']}'";
			} else if(isset($args['appSecret']) && !empty($args['appSecret'])) {
				$where = "appSecret='{$args['appSecret']}'";
			}
			
			if(!empty($where) && is_string($where)) {
				$wechatsinfo = D('wx_wechats')->where($where)->find();
				//$this->logger->debug(D('wechats')->_sql());
				if(!empty($wechatsinfo) && is_array($wechatsinfo)) {
				
					$arrat = json_decode($wechatsinfo['jsapi_ticket'], true);
					if(!empty($arrat) && is_array($arrat) && $arrat['expire_time'] > NOW_TIME) {
						$ret = $arrat['jsapi_ticket'];
					} else {
						$accessToken = $this->getWxPubAccessToken($args);
						$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token={$accessToken}";
      					$token = json_decode(https_request($url), true);
						if ($token['jsapi_ticket']) {
							$data['jsapi_ticket'] = $token['jsapi_ticket'];
							$data['expire_time']  = NOW_TIME + 7000;
			
							$json_data = json_encode($data);
	
							D('wx_wechats')->where($where)->save(array('jsapi_ticket'=>$json_data));
							$ret = $token['jsapi_ticket'];
							//$this->logger->debug(array(2222, $ret));
						} else {
							$this->logger->error(array(__METHOD__, '未从微信官方接口获得jsapi_ticket数据', func_get_args()));
						}
					}
				} else {
					$this->logger->error(array(__METHOD__, 'DB中无此微信公众号的数据', func_get_args()));
				}
			} else {
				$this->logger->error(array(__METHOD__, '参数缺失或错误', func_get_args()));
			}
		}
		return $ret;
	}

	/**
	 * 获取用户信息(有openid的用户)
	 * $appid = 当前微信公众帐号的AppId
	 * $appsecret = 当前微信公众帐号的AppSecret
	 * $openid = 用户openID
	 * $size = 用户头像的尺寸
	 *
	 * 成功则返回11个值的信息
	 * subscribe		=>是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息
	 * openid			=>用户的标识，对当前公众号唯一
	 * nickname			=>用户的昵称
	 * sex				=>用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
	 * language			=>用户的语言，简体中文为zh_CN
	 * city				=>用户所在城市
	 * province			=>用户所在省份
	 * country			=>用户所在国家
	 * headimgurl		=>用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空 
	 * subscribe_time	=>用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
	 * remark			=>用户备注
	 */
	public function wei_user_content($appid,$appsecret,$openid,$size="0"){
		//获取微信所给出的access_token
		//$access_token = $this->wei_access_token($appid,$appsecret);
		$access_token = $this->getWxPubAccessToken(array('appId'=>$appid, 'appSecret'=>$appsecret));
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
	}

	/**
	 * 用户同意授权，获取临时的 code 值
	 *
	 * $appid		=> AppID(应用ID)
	 * $returnurl	=> 返回到项目页面的链接地址
	 * $type		=> 类型(为真需要用户授权，为假默认用户已授权)
	 *
	 * 这里返回打开微信的链接地址，在方法里直接跳转到当前返回的链接即可
	 * 用户需授权或无需授权，成功后返回所给链接地址页面，格式为：$returnurl/?code=返回临时的 code 值&state=STATE
	 */
	public function wei_code_url($appid,$returnurl,$type=true){
		$scope=$type?"snsapi_userinfo":"snsapi_base";
		//使用urlencode对链接进行处理
		$returnurl=urlencode($returnurl);
		//$weixinurl="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$returnurl."&response_type=code&scope=".$scope."&state=STATE#wechat_redirect";
		$weixinurl="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$returnurl."&response_type=code&scope=".$scope."&state=1#wechat_redirect";
		return $weixinurl;
	}

	/**
	 * 通过code获取网页授权access_token 返回openID 并获取用户信息
	 *
	 * $appid		=> AppID(应用ID)
	 * $secret		=> AppSecret(应用密钥)
	 * $code		=> 获取的临时 code 值
	 * $size		=> 用户头像的尺寸
	 * $type		=> 为真返加用户信息,为假返回当前获取的授权信息
	 * $grant_type	=> 填写为authorization_code
	 */
	public function wei_accesstoken_user($appid,$secret,$code,$size="0",$type=true){
		$get_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
		$get_return = file_get_contents($get_url);
		$get_return = json_decode($get_return , true);
		if($type){
			return $this->wei_get_user_content($get_return['openid'],$get_return['access_token'],$size);
		}else{
			return $get_return;
		}
	}

	/**
	 * 获取用户信息(需授权的用户)
	 * $accesstoken = 通过网址获取的 access_token
	 * $openid = 用户openID
	 * $size = 用户头像的尺寸
	 *
	 * 成功则返回9个值的信息
	 * openid		=>用户的唯一标识
	 * nickname		=>用户昵称
	 * sex			=>用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
	 * province		=>用户个人资料填写的省份
	 * city			=>普通用户个人资料填写的城市
	 * country		=>国家，如中国为CN
	 * headimgurl	=>用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像）
	 * privilege	=>用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
	 * unionid		=>只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。详见：获取用户个人信息（UnionID机制）
	 */
	public function wei_get_user_content($openid,$accesstoken,$size="0"){
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
	}

}

?>