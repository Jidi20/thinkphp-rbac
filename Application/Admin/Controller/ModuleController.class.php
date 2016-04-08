<?php
namespace Admin\Controller;
use Think\CommonController;
/**
 * 模块开发管理；
 * @author [Jidi]
 */
class ModuleController extends CommonController {
	/**
	 * [后台管理首页]
	 * @return [type] [description]
	 */
    public function index(){
       //搜索
        $where = '1=1';
        $keyword = I('get.keyword');
        if($keyword != ''){
            $where .= " AND name LIKE '%{$keyword}%' OR title LIKE '%{$keyword}%'";
        }
        $count = parent::_count('admin_module');
        $Page = new \Think\Page($count,300);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','首页');
        $Page->setConfig('last','尾页');
        $list = M('admin_module')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->where($where)
                ->select();
        if($keyword ==''){
            $list = unlimitedForLevel($list,'&nbsp;&nbsp;&nbsp;--');
        }
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->display();
    }

    /**
     * [模块添加页]
     */
    public function add(){
    	$list = parent::_select('admin_module');
		$list = unlimitedForLevel($list,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
		$this->assign('list',$list);
    	$this->display();
    }
    /**
     * [模块ajax添加]
     * @return [type] [description]
     */
    public function add_ajax(){
		if(false==$post=$this->add_check()){
			$data['success']=0;
			$data['message']=$this->msg;
		}else{
            $m_list=parent::_find("admin_module",array("id"=>$post['pid']),"o_level");
            $post['o_level']=$m_list['o_level']+1;
			if(parent::_insert($post,'admin_module')){
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
     * [模块编辑页]
     */
    public function edit(){
        $list = parent::_select('admin_module');
        $list = unlimitedForLevel($list,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        $id = I('get.id');
        $module = parent::_find('admin_module',"id='{$id}'");
        $this->assign('list',$list);
        $this->assign('module',$module);
        $this->display();
    }

    /**
     * [模块ajax编辑]
     * @return [type] [description]
     */
    public function edit_ajax(){
        if(false==$post=$this->add_check()){
            $data['success']=0;
            $data['message']=$this->msg;
        }else{
            if(M('admin_module')->where(array('id'=>$post['id']))->save($post)){
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
     * [删除单个]
     * @return [type] [description]
     */
    public function delete_one(){
        $id = I('post.id');
        //根据传入的id，判断其是否还有子级id，如果有则不能删除
        $son_ids = parent::_select('admin_module',"pid='{$id}'",'id');
        if(!empty($son_ids)){
            $data['success']=2;
            $data['message']="操作失败，该分类下有子级分类不能删除，请先删除子分类";
        }else{
            if(M('admin_module')->delete($id)){
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
     * [删除多个]
     * @return [type] [description]
     */
    public function delete_all(){
        $id = I('post.ids');
        if(!empty($id)){
            $i = 0;
            $ids = array_filter(explode(' ', $id));
            foreach($ids as $v){
                if(M('admin_module')->delete($v)){
                    $i++;
                }
            }
            if(count($ids) == $i){
                $data['success'] = 1;
                $data['message'] = '操作成功';
            }else{
                $data['success'] = 0;
                $data['message'] = '部分未操作成功';
            }
        }
        $this->ajaxReturn($data,"JSON");
    }

    /**
     * [错误分析提示]
     * @return [type] [数组]
     */
    private function add_check(){
    	$post = I('post.');
    	if(empty($post['name'])){
			$this->msg="模块名称不能为空";
			return false;
		}elseif(empty($post['title'])){
			$this->msg="英文名称不能为空";
			return false;
		}else{
			return $post;
		}
    }

}