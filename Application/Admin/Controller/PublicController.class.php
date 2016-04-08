<?php
namespace Admin\Controller;
use Think\CommonController;
class PublicController extends CommonController {
    public function index() {
        if (!session("?" . C('USER_AUTH_KEY'))) {
            redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
        } else {
            //登录成功，获得模块列表
            parent::getModuleList();
            redirect(__MODULE__ . '/Index');
        }
    }

    public function login() {
        if (!session("?" . C('USER_AUTH_KEY'))) {
            layout(false);
            $this->display();
        } else {
            //登录成功，获得模块列表
            parent::getModuleList();
			redirect(__MODULE__.'/Index');
        }
    }

    // 用户登出
    public function logout() {
        if (session("?" . C('USER_AUTH_KEY'))) {
            session(null);
            $this->assign("jumpUrl", PHP_FILE . C('USER_AUTH_GATEWAY'));
            $this->success('登出成功！', __MODULE__ . "/Public/login");
        } else {
            $this->error('已经登出！');
        }
    }

    //验证码
    public function verify() {
        $verify = new \Think\Verify();
        $verify->length = 4;
        $verify->fontSize = 13;
        $verify->useNoise = false;
        $verify->useCurve = false;
        $verify->codeSet = '0123456789';
        $verify->entry();
    }


    // 登录检测
    public function checkLogin() {
        $phone = I('post.phone');
        $password = trim(I('post.password'));
        $verify = I('post.verify');
        if (empty($phone)) {$this->error('帐号不能为空！');
        } elseif (empty($password)) {$this->error('密码不能为空！');
        }elseif(empty($verify)){$this->error('验证码不能为空！');}
        $time = time();
        //查询条件
        $map = array();
        $map['phone'] = $phone;
        $map["is_lock"] = array('eq', 1);//账号状态为1，可登陆
        $table = "admin";
        $userinfo = parent::_find($table,$map);
        if(md5($verify) != session('verify')){
            $this->error('验证码错误！');exit();
        }

        if(!$userinfo){
            $this->error('不是有效的账号！');
        }elseif($userinfo['password'] == parent::encodepassword($password,$userinfo['salt'])){
            session(C('USER_AUTH_KEY'),$userinfo['id']);
            session('name',$userinfo['name']);
            session('phone',$userinfo['phone']);
            session('is_admin',$userinfo['is_admin']);
            session('login_time',$time);
            session('login_ip',$userinfo['login_ip']);
            session('role_id',$userinfo['role_id']);
            session('job',$userinfo['job']);
            session('admin_id',$userinfo['id']);
            if ($userinfo['is_admin'] == 1) {
                session('administrator',true);
            }
            //保存登录信息
            $ip = get_client_ip(1);
            $data = array();
            $data['id'] = $userinfo['id'];
            $data['login_time'] = $time;
            $data['login_count'] = array('exp', 'login_count+1');
            $data['login_ip'] = $ip;
            parent::_update($data,$table);

            //读取模块列表；写入session；在layout页面读session显示
            parent::getModuleList();
            redirect(__MODULE__.'/Index');
        }else{
           $this->error('密码错误！');
        }
    }

    // 检测输入的验证码是否正确，$code为用户输入的验证码字符串
    public function check_verify($code, $id = ''){
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }

}
