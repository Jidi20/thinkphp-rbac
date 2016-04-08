<?php
namespace Admin\Controller;
use Think\CommonController;
/**
 * 后台首页管理；
 * @author [Jidi]
 */
class IndexController extends CommonController {
	/**
	 * [后台管理首页]
	 * @return [type] [description]
	 */
    public function index(){
        $list = session('nav_list');
        $list = gettree2($list);
        $this->assign('list',$list);
    	$this->display();
    }

    /**
     * [个人信息]
     * @return [type] [description]
     */
    public function infoshow(){
        $id = session('admin_id');
        if(session('is_admin')==1){
            $list = parent::_find('admin',"id='{$id}'");
        }else{
            $list = parent::_joinFind('admin as a',array(array('role as r on a.role_id=r.id','LEFT')),'a.* r.name as rolename');
        }
        $this->assign('list',$list);
        $this->display();
    }

    /**
     * [修改密码页]
     * @return [type] [description]
     */
    public function changepassword(){
        $this->display();
    }
    /**
     * [密码修改处理]
     * @return [type] [description]
     */
    public function dochangepass(){
        $admin_id = session('admin_id');
        $table = session('admin_table');//不同管理员登录后，获得该管理员所属的表
        $oldpass = parent::_getField($table,'password',"id='{$admin_id}'");
        $salt = parent::_getField($table,'salt',"id='{$admin_id}'");
        $oldpassword = I('post.oldpassword');
        $newpassword = I('post.newpassword');
        $newpassword1 = I('post.newpassword1');
        if($oldpassword ==''){
            $data['success'] = 0;
            $data['message'] = '原密码不能为空，请重试';
            $this->ajaxReturn($data,"JSON");exit();
        }
        if($newpassword == ''){
            $data['success'] = 0;
            $data['message'] = '新密码不能为空，请重试';
            $this->ajaxReturn($data,"JSON");exit();
        }
        if($newpassword1 == ''){
            $data['success'] = 0;
            $data['message'] = '确认新密码不能为空，请重试';
            $this->ajaxReturn($data,"JSON");exit();
        }
        if($newpassword != $newpassword1){
            $data['success'] = 0;
            $data['message'] = '两次密码不一致';
            $this->ajaxReturn($data,"JSON");exit();
        }
        $oldpassword = parent::encodepassword($oldpassword,$salt);
        if($oldpassword == $oldpass){
            $post['salt'] = getrandstr(4);
            $post['password'] =  parent::encodepassword($newpassword,$post['salt']);
            if(M($table)->where(array('id'=>$admin_id))->save($post)){
                $data['success'] = 1;
                $data['message'] = '操作成功';
            }else{
                $data['success'] = 0;
                $data['message'] = '操作失败，请重试';
            }
        }else{
            $data['success'] = 0;
            $data['message'] = '原密码错误，请重试';
        }
        $this->ajaxReturn($data,"JSON");
    }

}