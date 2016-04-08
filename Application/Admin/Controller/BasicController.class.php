<?php
namespace Admin\Controller;
use Think\CommonController;
/**
 * 基本设置；
 * @author [Jidi]
 */
class BasicController extends CommonController {
	/**
	 * 首页,角色列表
	 */
    public function index(){
        //搜索
        $where = '1=1';
        $keyword = I('get.keyword');
        if($keyword != ''){
            $where .= " AND name LIKE '%{$keyword}%' OR duty LIKE '%{$keyword}%'";
        }
        $count = parent::_count('role');
        $Page = new \Think\Page($count,30);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','首页');
        $Page->setConfig('last','尾页');
        $list = M('role')
                ->field('name,id,duty')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->where($where)
                ->select();
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
    	$this->display();
    }

    /**
     * [网站信息--设置页、添加、编辑]
     */
    public function siteadd(){
        if(IS_AJAX){
            if(false==$post=$this->site_add_check()){
                $data['success']=0;
                $data['message']=$this->msg;
            }else{
                if($post['id'] == ''){
                    if(parent::_insert($post,'config')){
                        $data['success']=1;
                        $data['message']="操作成功";
                    }else{
                        $data['success']=0;
                        $data['message']="操作失败，请重试";
                    }
                }else{
                    if(M('config')->where(array('id'=>$post['id']))->save($post)){
                        $data['success']=1;
                        $data['message']="操作成功";
                    }else{
                        $data['success']=0;
                        $data['message']="操作失败，请重试";
                    }
                }
            }
            $this->ajaxReturn($data,"JSON");
        }
        $list = parent::_find('config',"id=1");
        $this->assign('list',$list);
    	$this->display();
    }

    /**
     * [角色--列表]
     * @return [type] [description]
     */
    public function rolelist(){
        //搜索
        $where = '1=1';
        $keyword = I('get.keyword');
        if($keyword != ''){
            $where .= " AND name LIKE '%{$keyword}%' OR duty LIKE '%{$keyword}%'";
        }
        $count = parent::_count('role');
        $Page = new \Think\Page($count,30);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','首页');
        $Page->setConfig('last','尾页');
        $list = M('role')
                ->field('name,id,duty')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->where($where)
                ->select();
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->display();
    }
    /**
     * [角色--ajax添加]
     * @return [type] [description]
     */
    public function role_add_ajax(){
		if(false==$post=$this->role_add_check()){
			$data['success']=0;
			$data['message']=$this->msg;
		}else{
			if(parent::_insert($post,'role')){
				$data['success']=1;
				$data['message']="操作成功";
			}else{
				$data['success']=0;
				$data['message']="操作失败，请重试";
			}
		}
    	$this->ajaxReturn($data,"JSON");
    }

    /**
     * [角色--编辑页]
     */
    public function roledit(){
        $id = I('get.id');
        $list = parent::_find('role',"id='{$id}'");
        $this->assign('list',$list);
        $this->display();
    }

    /**
     * [角色--ajax编辑]
     * @return [type] [description]
     */
    public function role_edit_ajax(){
        if(false==$post=$this->role_add_check()){
            $data['success']=0;
            $data['message']=$this->msg;
        }else{
            if(M('role')->where(array('id'=>$post['id']))->save($post)){
                $data['success']=1;
                $data['message']="操作成功";
            }else{
                $data['success']=0;
                $data['message']="操作失败，请重试";
            }
        }
        $this->ajaxReturn($data,"JSON");
    }
    /**
     * [角色--ajax删除单个]
     * @return [type] [description]
     */
    public function role_delete(){
        $id = I('post.id');
        if(M('role')->delete($id)){
            $data['success']=1;
        }else{
            $data['success']=0;
            $data['message']="操作失败，请重试";
        }
        $this->ajaxReturn($data,"JSON");
    }

    /**
     * [人员管理--列表,代理级别]
     * @return [type] [description]
     */
    public function admin_list(){
        //搜索
        $where = 'is_admin=0';
        $keyword = I('get.keyword');
        if($keyword != ''){
            $where .= " AND is_admin <> 1 AND name LIKE '%{$keyword}%' OR phone LIKE '%{$keyword}%'";
        }
        $count = parent::_count('admin');
        $Page = new \Think\Page($count,30);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','首页');
        $Page->setConfig('last','尾页');
        $list = M('admin')
                ->where($where)
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->display();
        $this->display();
    }
    /**
     * [人员--添加页]
     * @return [type] [description]
     */
    public function admin_add(){
        $list = parent::_select('role','','id,name');
        $this->assign('list',$list);
        $this->display();
    }
    /**
     * [人员--ajax添加]
     * @return [type] [description]
     */
    public function admin_add_ajax(){
        if(false==$post=$this->admin_add_check()){
            $data['success']=0;
            $data['message']=$this->msg;
        }else{
            $post['salt'] = getrandstr(4);
            $post['password'] =  parent::encodepassword($post['password'],$post['salt']);
            if(parent::_insert($post,'admin')){
                $data['success']=1;
                $data['message']="操作成功";
            }else{
                $data['success']=0;
                $data['message']="操作失败，请重试";
            }
        }
        $this->ajaxReturn($data,"JSON");
    }
    /**
     * [人员--ajax删除单个]
     * @return [type] [description]
     */
    public function admin_delete(){
        $id = I('post.id');
        if(M('admin')->delete($id)){
            $data['success']=1;
            $data['message']="操作成功";
        }else{
            $data['success']=0;
            $data['message']="操作失败，请重试";
        }
        $this->ajaxReturn($data,"JSON");
    }

    /**
     * [人员--编辑页]
     */
    public function admin_edit(){
        //角色列表
        $role_list = parent::_select('role','','id,name');
        $id = I('get.id');
        $list = parent::_find('admin',"id='{$id}'");

        $this->assign('role_list',$role_list);
        $this->assign('list',$list);
        $this->display();
    }
    /**
     * [人员--ajax编辑]
     * @return [type] [description]
     */
    public function admin_edit_ajax(){
        if(false==$post=$this->admin_add_check()){
            $data['success']=0;
            $data['message']=$this->msg;
        }else{
            $post['is_lock'] = $post['is_lock'];
            $post['salt'] = getrandstr(4);
            $post['password'] =  parent::encodepassword($post['password'],$post['salt']);
            if(M('admin')->where(array('id'=>$post['id']))->save($post)){
                $data['success']=1;
                $data['message']="操作成功";
            }else{
                $data['success']=0;
                $data['message']="操作失败，请重试";
            }
        }
        $this->ajaxReturn($data,"JSON");
    }

    /**
     * [权限分配--角色列表]
     * @return [type] [description]
     */
    public function node_list(){
        //搜索
        $where = '1=1';
        $keyword = I('get.keyword');
        if($keyword != ''){
            $where .= " AND name LIKE '%{$keyword}%' OR duty LIKE '%{$keyword}%'";
        }
        $count = parent::_count('role');
        $Page = new \Think\Page($count,30);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','首页');
        $Page->setConfig('last','尾页');
        $list = M('role')
                ->where($where)
                ->field('id,name,duty')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->display();
    }
    /**
     * [权限--添加页]
     */
    public function node_add(){
        $id = I('get.id');

        $nav_list = parent::_select('admin_module',"status=1", "id,name,title,pid",'',"sort DESC");
        $list = gettree2($nav_list);
        //该角色现有的模块
        $role_module_list = parent::_getField('module','id,module_id',"role_id='{$id}'");
        $module_ids = implode(',', $role_module_list);

        $this->assign('id',$id);
        $this->assign('list',$list);
        $this->assign('module_ids',$module_ids);
        $this->display();
    }

    /**
     * [添加ajax]
     * @return [type] [description]
     */
    public function node_add_ajax(){
        $arr = I('post.module_id');
        $id = I('post.id');
        $i = 0;
        M('module')->where("role_id='{$id}'")->delete();
        foreach($arr as $v){
            $data = array(
                'role_id' => $id,
                'module_id' => $v
                );
            if(parent::_insert($data,'module')){
                $i++;
            }
        }
        if($i==count($arr,0)){
            $this->success('操作成功', 'node_list');
        }else{
            $this->error('操作失败，请重试');
        }
    }

    /**
     * [角色添加--错误分析提示]
     * @return [type] [数组]
     */
    private function role_add_check(){
    	$post = I('post.');
    	if(empty($post['name'])){
			$this->msg="角色名称不能为空";
			return false;
		}elseif(empty($post['duty'])){
			$this->msg="角色职务不能为空";
			return false;
		}else{
			return $post;
		}
    }

    /**
     * [网站信息设置--错误分析提示]
     * @return [type] [数组]
     */
    private function site_add_check(){
        $post = I('post.');
        if(empty($post['sitename'])){
            $this->msg="网站名称不能为空";
            return false;
        }elseif(empty($post['siteurl'])){
            $this->msg="网站地址不能为空";
            return false;
        }elseif(empty($post['email'])){
            $this->msg="网站管理员邮箱不能为空";
            return false;
        }elseif(empty($post['record_num'])){
            $this->msg="网站管理员邮箱不能为空";
            return false;
        }elseif(empty($post['copyright'])){
            $this->msg="底部版权信息不能为空";
            return false;
        }elseif(empty($post['title'])){
            $this->msg="网站管理员邮箱不能为空";
            return false;
        }elseif(empty($post['keyword'])){
            $this->msg="网站关键字不能为空";
            return false;
        }elseif(empty($post['description'])){
            $this->msg="网站描述不能为空";
            return false;
        }else{
            return $post;
        }
    }

    /**
     * [人员--错误分析提示]
     * @return [type] [数组]
     */
    private function admin_add_check(){
        $post = I('post.');
        if(empty($post['name'])){
            $this->msg="名称不能为空";
            return false;
        }elseif(empty($post['phone'])){
            $this->msg="手机号不能为空";
            return false;
        }elseif(empty($post['password'])){
            $this->msg="密码不能为空";
            return false;
        }else{
            return $post;
        }
    }
}