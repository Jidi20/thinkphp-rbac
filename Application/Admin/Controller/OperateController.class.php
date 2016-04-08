<?php
namespace Admin\Controller;
use Think\CommonController;
/**
 * 运营设置；
 * @author [Jidi]
 */
class OperateController extends CommonController {
	/**
     * [区域--列表页]
     * @return [type] [description]
     */
    public function index(){
        //搜索
        $where = '1=1';
        $keyword = I('get.keyword');
        if($keyword != ''){
            $where .= " AND province LIKE '%{$keyword}%' OR city LIKE '%{$keyword}%' OR area LIKE '%{$keyword}%'";
        }

        $count = parent::_count('area');
        $Page = new \Think\Page($count,30);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $Page->setConfig('first','首页');
        $Page->setConfig('last','尾页');
        $list = M('area')
                ->where($where)
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();

        $this->assign('list',$list);
        $this->assign('page',$Page->show());
    	$this->display();
    }

    /**
     * [区域--添加页]
     */
    public function area_add(){
    	$this->display();
    }
    /**
     * [区域--ajax添加]
     * @return [type] [description]
     */
    public function area_add_ajax(){
		if(false==$post=$this->area_add_check()){
			$data['success']=0;
			$data['message']=$this->msg;
		}else{
			if(parent::_insert($post,'area')){
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
     * [区域--ajax删除单个]
     * @return [type] [description]
     */
    public function area_delete(){
        $id = I('post.id');
        if(M('area')->delete($id)){
            $data['success']=1;
        }else{
            $data['success']=0;
            $data['message']="操作失败，请重试";
        }
        $this->ajaxReturn($data,"JSON");
    }

    /**
     * [区域--错误分析提示]
     * @return [type] [数组]
     */
    private function area_add_check(){
    	$post = I('post.');
    	if(empty($post['province'])){
			$this->msg="省不能为空";
			return false;
		}elseif(empty($post['city'])){
			$this->msg="市不能为空";
			return false;
		}elseif(empty($post['area'])){
            $this->msg="区县不能为空";
            return false;
        }else{
			return $post;
		}
    }
}