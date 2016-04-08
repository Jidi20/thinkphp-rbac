<?php
namespace Think;

/**
 * ThinkPHP 控制器基类
 */
class CommonController extends Controller
{
    protected $encodemethod;
    protected $action_name;//方法名称
    protected $logger = null; //写日志对象
    
    
    function __construct() {
        parent::__construct();
        $this->encodemethod = C("AUTH_PWD_ENCODER");
        //登录权限访问
        $no_auth_module = C("NOT_AUTH_MODULE");// 默认无需认证模块
        $module = ucfirst(CONTROLLER_NAME);//控制器名称
        //登录后模块访问权限
        $this->action_name=ACTION_NAME;
        if (C('USER_AUTH_ON') && !in_array($module, array_ucfirst(explode(",", $no_auth_module)))) {
            $this->checkModule();
        }

        if(empty($this->logger)) {
	        import("Org.Util.Logger");
			$this->logger = new \Org\Util\Logger();
        }
        
    }
    /**
     * [检测访问权限]
     * @return [type]             [description]
     */
    function checkModule() {
        $rbacname=session('nav_list');
        if(session('is_admin'))return true;
        if(in_array2($this->action_name,$rbacname) || $this->action_name == 'index' || $this->action_name == 'add' || $this->action_name == 'edit'){
            return true;
        }else{
            if(IS_AJAX){
                $data['success']=0;
                $data['message']="没有操作权限";
                $this->ajaxReturn($data,"JSON");
            }else{
                $this->error('没有操作权限');
            }
        }
    }
    /**
     * [读取模块列表]
     * @return [type] [description]
     */
    protected function getModuleList(){
        $is_admin = session('is_admin');
        if($is_admin==1){
            $nav_list = $this->_select('admin_module',"status=1", "id,name,title,pid,is_view,o_level",'',"sort DESC,id ASC");
            session('nav_list',$nav_list);
        }elseif($is_admin==0){
            //非超级管理员，代理
            $role_id = session('role_id');
            $nav_list = $this->_joinSelect('module as m',array(array('m_admin_module as a on m.module_id=a.id','LEFT')),"a.status=1 AND m.role_id='{$role_id}'", "a.id,a.name,a.title,a.pid,a.is_view,a.o_level",'',"a.sort DESC,a.id ASC");
            session('nav_list',$nav_list);
        }
    }

    /**
     * [encodepassword 加密密码]
     *
     * @param  string $pwd  [明码]
     * @param  string $salt [盐]
     *
     * @return [type]       [description]
     */
    protected function encodepassword($pwd = "", $salt = "") {
        $encodepwd = $this->encodemethod;
        return $encodepwd($encodepwd($pwd) . $salt);
    }
    /**
     * [_select 查询记录]
     *
     * @param  [type] $table [表名]
     * @param  [type] $where [where条件]
     * @param  string $field [查询字段]
     * @param  [type] $limit [限制查询条数，常用于分页]
     * @param  string $sort  [p排序]
     * @param  string $m     [可以自定义调用模型的方法]
     *
     * @return [type]        [二维数组]
     */
    protected function _select($table = null, $where = null, $field = "*", $limit = null, $sort = "", $m = "m") {
        if (empty($table)) return "_select方法缺少表名";
        if (strtolower($m) == "d") {
            $model = D($table);
        }
        else {
            $model = M($table);
        }
        return $model->where($where)->field($field)->order($sort)->limit($limit)->select();
    }
    /**
     * [_getCol 多条一个值]
     * @param  [type] $table [表名]
     * @param  [type] $where [查询条件]
     * @param  string $field [查询字段]
     * @param  [type] $limit [限制查询数量，分页查询]
     * @param  string $sort  [排序]
     * @param  string $m     [模型实例化，默认M，或者D]
     * @return [type]        [某字段的一维数组]
     */
    protected function _getCol($table = null, $where = null, $field = "*", $limit = null, $sort = "", $m = "m") {
        if (empty($table)) return "_getCol方法缺少表名";
        if (strtolower($m) == "d") {
            $model = D($table);
        }
        else {
            $model = M($table);
        }
        $sl = $model->where($where)->field($field)->order($sort)->limit($limit)->select();
        $tmp = array();
        if (!empty($sl)) {
            foreach ($sl as $key => $val) {
                $keys = array_keys($val);
                $key_s = $keys[0];
                $tmp[] = $val[$key_s];
            }
        }
        return $tmp;
    }

    /**
     * [_joinSelect 连表查询] $join array(array('***','LEFT'))
     * @param  [type] $table  [表名]
     * @param  array  $joins  [连表]
     *         例：parent::_joinSelect('table_a as a',array(array(C('DB_PREFIX').'table_b as b on a.id=b.id','LEFT'),array(C('DB_PREFIX').'table_c as s on c.id=a.id','LEFT')),$where,'field');
     * @param  [type] $where  [查询条件]
     * @param  string $field  [查询字段]
     * @param  [type] $limit  [限制查询数量，分页查询]
     * @param  [type] $sort   [排序]
     * @param  [type] $group  [分组]
     * @param  [type] $having [用于配合group方法完成从分组的结果中筛选（通常是聚合条件）数据]
     * @param  string $m      [模型实例化，默认M，或者D]
     * @return [type]         [二维数组]
     */
    protected function _joinSelect($table = null, $joins = array(), $where = null, $field = "*", $limit = null, $sort = null, $group = null, $having = null, $m = "m") {
        if (empty($table)) return "_joinSelect方法缺少表名";
        if (empty($joins)) return "_joinSelect方法缺少关联信息";
        if (strtolower($m) == "d") {
            $model = D($table);
        }
        else {
            $model = M($table);
        }
        if (is_array($joins)) {
            if (is_string($joins[0])) {
                return $model->join($joins[0], $joins[1])->where($where)->field($field)->order($sort)->limit($limit)->group($group)->having($having)->select();
            }
            else {
                $obj = $model->where($where)->field($field);
                foreach ($joins as $value) {
                    $obj = $obj->join($value[0], $value[1]);
                }
                return $obj->order($sort)->limit($limit)->group($group)->having($having)->select();
            }
        }
        else {
            return "_joinSelect方法关联信息参数格式不对";
        }
    }

    /**
     * [_find 单条数据]
     * @param  [type] $table [表名]
     * @param  [type] $where [查询条件]
     * @param  string $field [查询字段]
     * @param  [type] $sort  [排序]
     * @param  string $m     [模型实例化，默认M，或者D]
     * @return [type]        [一维数组]
     */
    protected function _find($table = null, $where = null, $field = "*", $sort = null, $m = "m") {
        if (empty($table)) return "_find方法缺少表名";
        if (strtolower($m) == "d") {
            $model = D($table);
        }
        else {
            $model = M($table);
        }
        return $model->where($where)->field($field)->order($sort)->find();
    }

    /**
     * [_find 单条数据] $join array(array('***','LEFT')) 连表查询
     * @param  [type] $table [表名]
     * @param  [type] $where [查询条件]
     * @param  string $field [查询字段]
     * @param  [type] $sort  [排序]
     * @param  string $m     [模型实例化，默认M，或者D]
     * @return [type]        [一维数组]
     */
    protected function _joinFind($table = null, $joins = array(), $where = null, $field = "*", $sort = null, $m = "m") {
        if (empty($table)) return "_joinFind方法缺少表名";
        if (empty($joins)) return "_joinFind方法缺少关联信息";
        if (strtolower($m) == "d") {
            $model = D($table);
        }
        else {
            $model = M($table);
        }
        if (is_array($joins)) {
            if (is_string($joins[0])) {
                return $model->join($joins[0], $joins[1])->where($where)->field($field)->order($sort)->find();
            }
            else {
                $obj = $model->where($where)->field($field);
                foreach ($joins as $value) {
                    $obj = $obj->join($value[0], $value[1]);
                }
                return $obj->order($sort)->find();
            }
        }
        else {
            return "_joinFind方法关联信息参数格式不对";
        }
    }

    /**
     * [_getField 查询某字段] $join array(array('***','LEFT')) 可以连表或不连表
     * @param  [type] $table [表名]
     * @param  [type] $field [查询字段]
     * @param  [type] $where [查询条件]
     * @param  array  $joins [连表]
     * @param  string $m     [模型实例化，默认M，或者D]
     * @return [type]        [连表查询某字段]
     */
    protected function _getField($table = null, $field = null, $where = null, $joins = array(), $m = "m") {
        if (empty($table)) return "_getField方法缺少表名";
        if (empty($field)) return "_getField方法缺少字段名";
        if (strtolower($m) == "d") {
            $model = D($table);
        }
        else {
            $model = M($table);
        }
        if (is_string($joins[0])) {
            return $model->join($joins[0], $joins[1])->where($where)->getField($field);
        }
        else {
            $obj = $model->where($where);
            foreach ($joins as $value) {
                $obj = $obj->join($value[0], $value[1]);
            }
            return $obj->getField($field);
        }
    }

    /**
     * [_count 统计数目] $join array(array('***','LEFT'))
     * @param  [type] $table [表名]
     * @param  [type] $where [查询条件]
     * @param  array  $joins [连表]
     * @param  [type] $group [分组]
     * @param  string $m     [模型实例化，默认M，或者D]
     * @return [type]        [统计数目]
     */
    protected function _count($table = null, $where = null, $joins = array(), $group = null, $m = "m") {
        if (empty($table)) return "_getField方法缺少表名";
        if (strtolower($m) == "d") {
            $model = D($table);
        }
        else {
            $model = M($table);
        }
        if (is_string($joins[0])) {
            return $model->join($joins[0], $joins[1])->where($where)->group($group)->having($having)->count();
        }
        else {
            $obj = $model->where($where);
            foreach ($joins as $value) {
                $obj = $obj->join($value[0], $value[1]);
            }
            return $obj->group($group)->having($having)->count();
        }
    }

    /**
     * [index 自动生成列表，带分页,单表]
     * 连表查询示例
     * $where = "a.shop_id= c.id and a.category_id = b.id and a.print_id = e.id and a.goods_unit = d.id and a.goods_name like '%" . I('post.goods_name') . "%' and c.id=" . $shop_id . " and a.del_status = 0 ";
     * $field = "a.*,b.name as category_name,c.name as shop_name,d.name as unit_name,e.print_goods_category as print_category_name";
     * $this->join = array('tablename' => array(C('DB_PREFIX') . "goods" => 'a', C('DB_PREFIX') . "goods_category" => 'b', C('DB_PREFIX') . "shop" => 'c',C('DB_PREFIX') . "goods_unit" => 'd',C('DB_PREFIX') . "shop_printer" => 'e'), 'where' => $where, 'field' => $field);
     * @param  [type] $_name [表名]
     * @return [type]        [description]
     */
    protected function _index($_name = null) {
        //列表过滤器，生成查询Map对象
        $map = $this->_search();
        $serach_where = I("post.");
        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        if (!$_name) $_name = $this->getControllerName();
        $model = D($_name);
        if (!empty($model)) {
            $this->_list($model, $map);
        }
        $this->assign('map', $serach_where);
        $this->display();
        return;
    }

    /**
     * 根据表单生成查询条件
     * 进行列表过滤
     * @access protected
     * @param Model $model 数据对象
     * @param HashMap $map 过滤条件
     * @param string $sortBy 排序
     * @param boolean $asc 是否正序
     * @return void
     * @throws ThinkExecption
     */
    private function _list($model, $map, $sortBy = '', $asc = true) {
        //排序字段 默认为主键名
        if (!empty($_REQUEST['_order'])) {
            $order = $_REQUEST['_order'];
        }
        else {
            $order = !empty($sortBy) ? $sortBy : $model->getPk();
            if (!empty($this->join)) {
                $join = $this->join;
                $key = array_keys($join['tablename']);
                $pre = !empty($join['tablename'][$key[0]]) ? $join['tablename'][$key[0]] . "." : "";
            }
        }
        //排序方式默认按照倒序排列
        //接受 sort参数 0 表示倒序 非0都 表示正序
        if (isset($_REQUEST['_sort'])) {
            $sort = $_REQUEST['_sort'] == 'asc' ? 'asc' : 'desc';
        }
        else {
            $sort = $asc ? 'asc' : 'desc';
        }
        if (!empty($this->join)) {
            $join = $this->join;
            $key = array_keys($join['tablename']);
            $count = M()->table($join['tablename'])->where($join['where'])->count();
        }
        else {
            //取得满足条件的记录数
            $count = $model->where($map)->count('id');
        }
        if ($count > 0) {

            //创建分页对象
            if (!empty($_REQUEST['listRows'])) {
                $listRows = $_REQUEST['listRows'];
            }
            else {
                $listRows = '';
            }
            $p = new \Think\Page($count, $listRows);
            if (!empty($join)) {
                if (isset($join['field'])) {
                    $voList = M()->table($join['tablename'])->where($join['where'])->order("{$pre}`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->field($join['field'])->select();
                }
                else {
                    $voList = M()->table($join['tablename'])->where($join['where'])->order("{$pre}`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
                }
            }
            else {
                //分页查询数据
                $voList = $model->where($map)->order("`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select();
            }
            //分页跳转的时候保证查询条件
            foreach ($map as $key => $val) {
                if (!is_array($val)) {
                    $p->parameter.= "$key=" . urlencode($val) . "&";
                }
            }

            //分页显示
            $page = $p->show();
            
            //列表排序显示
            $sortImg = $sort;
            
            //排序图标
            $sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列';
            
            //排序提示
            $sort = $sort == 'desc' ? 1 : 0;
            
            //排序方式
            $this->_afterIndex($voList);
            
            //模板赋值显示
            $this->assign('list', $voList);
            $this->assign('sort', $sort);
            $this->assign('order', $order);
            $this->assign('sortImg', $sortImg);
            $this->assign('sortType', $sortAlt);
            $this->assign("page", $page);
        }
        $this->assign('totalCount', $count);
        $this->assign('numPerPage', $p->listRows);
        $this->assign('currentPage', !empty($_REQUEST[C('VAR_PAGE') ]) ? $_REQUEST[C('VAR_PAGE') ] : 1);
        return;
    }
    
    protected function _afterIndex(&$volist) {
    }
    
    /**
     * 根据表单生成查询条件
     * 进行列表过滤
     * @access protected
     * @param string $name 数据对象名称
     * @return HashMap
     * @throws ThinkExecption
     */
    protected function _search($_name = null) {
        
        //生成查询条件
        if (!$_name) $_name = $this->getControllerName();
        $model = D($_name);
        $map = array();
        foreach ($model->getDbFields() as $key => $val) {
            if (isset($_REQUEST[$val]) && $_REQUEST[$val] != '') {
                $map[$val] = $_REQUEST[$val];
            }
        }
        return $map;
    }
     /**
     * [_add 显示添加页面]
     */
    protected function add() {
        $this->display();
    }
    /**
     * [_read 显示编辑页面]
     */
    protected function read() {
        $this->edit();
    }

    function edit($_name = null) {
        if (!$_name) $_name = $this->getControllerName();
        $model = D($_name);
        $id = $_REQUEST[$model->getPk() ];
        $vo = $model->getById($id);
        $this->_afterEdit($vo);
        $this->assign('vo', $vo);
        $this->display();
    }

    protected function _afterEdit(&$vo) {
    }

    /**
     * [_insert 添加数据]
     * @param  string $data     [添加的数据]
     * @param  [type] $_name    [表名]
     * @param  [type] $callback [回调]
     * @param  string $type     [description]
     * @return [type]           [description]
     */
    protected function _insert($data = "", $_name = null, $callback = null, $type = "") {
        if (!$_name) $_name = $this->getControllerName();
        $model = D($_name);
        $create_data = $model->create($data, $type);
        if (false === $create_data) {
            $this->error($model->getError());
        }
        //保存当前数据对象
        $list = $model->add();
        if ($list !== false) {

            //保存成功
            //处理回调函数
            if ($callback && method_exists($this, $callback)) {
                call_user_func_array(array($this, $callback), array());
            }
            if (!$model->db()->isTrans) {
                //$this->success('新增成功!');
                return true;
            }
            else {
                $inset_id = $model->getLastInsID();
                return $inset_id;
            }
        }
        else {

            //失败提示
            if (!$model->db()->isTrans) {
                //$this->error('新增失败!');
                return false;
            }
            else {
                return false;
            }
        }
    }

    /**
     * [_insertAll 插入多条数据]
     *
     * @param  string $data     [二维数组]
     * @param  [type] $_name    [description]
     * @param  [type] $callback [description]
     * @param  string $type     [description]
     *
     * @return [type]           [description]
     */
    protected function _insertAll($data = "", $_name = null, $callback = null, $type = "") {
        if (!$_name) $_name = $this->getControllerName();
        $model = D($_name);
        $create_data = $model->create($data, $type);
        if (false === $create_data) {
            $this->error($model->getError());
        }

        //保存当前数据对象
        $list = $model->addAll($data);
        if ($list !== false) {
            //保存成功
            //处理回调函数
            if ($callback && method_exists($this, $callback)) {
                call_user_func_array(array($this, $callback), array());
            }
            if (!$model->db()->isTrans) {
                //$this->success('新增成功!');
                return true;
            } else {
                $inset_id = $model->getLastInsID();
                return $inset_id;
            }
        } else {
            //失败提示
            if (!$model->db()->isTrans) {
                //$this->error('新增失败!');
                return false;
            } else {
                return false;
            }
        }
    }

    /**
     * [_selectAdd 查询选择插入]
     *
     * @param  string $field     [插入的字段名]
     * @param  string $tablename [表名]
     *
     * @return [type]            [description]
     */
    function _selectAdd($field = "",$tablename = ""){
        return M()->_selectAdd($field,$tablename);
    }

    /**
     * [_update 修改数据]
     * @param  string $data     [数据]
     * @param  [type] $_name    [表名]
     * @param  [type] $callback [回调]
     * @param  string $type     [description]
     * @return [type]           [description]
     */
    protected function _update($data = "", $_name = null, $callback = null, $type = "") {
        if (!$_name) $_name = $this->getControllerName();
        $model = D($_name);
        $create_data = $model->create($data, $type);
        if (false === $create_data) {
            $this->error($model->getError());
        }

        // 更新数据
        $list = $model->save();

        if (false !== $list) {

            //处理回调函数
            if ($callback && method_exists($this, $callback)) {
                call_user_func_array(array($this, $callback), array($data[$model->getPk() ]));
            }

            //成功提示
            if (!$model->db()->isTrans) {
                $this->assign('jumpUrl', __URL__);
                //$this->success('编辑成功!');
                return true;
            }
            else {
                return true;
            }
        }
        else {

            //错误提示
            if (!$model->db()->isTrans) {
                //$this->error('编辑失败!');
                return false;
            }
            else {
                return false;
            }
        }
    }

    //获得事务处理的数据库模型
    protected function MC($_name = null) {
        if (!$_name) $_name = $this->getControllerName();
        return M($_name);
    }

    protected function DC($_name = null) {
        if (!$_name) $_name = $this->getControllerName();
        return D($_name);
    }

    /**
     * [_delete 删除数据，修改字段，标示为删除;del_status=>-1]
     * @param  [type] $_name    [description]
     * @param  [type] $callback [删除后，自定义回调函数]
     * @return [type]           [description]
     */
    protected function _delete($_name = null, $callback) {

        //删除指定记录
        if (!$_name) $_name = $this->getControllerName();
        $model = D($_name);
        if (!empty($model)) {
            $pk = $model->getPk();
            $id = $_REQUEST[$pk];
            if (isset($id)) {
                $condition = array($pk => array('in', explode(',', $id)));
                $list = $model->where($condition)->setField('del_status', -1);
                if ($list !== false) {

                    //处理回调函数
                    if ($callback && method_exists($this, $callback)) {
                        call_user_func_array(array($this, $callback), array(explode(',', $id)));
                    }
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }
    }

    /**
     * [_foreverdelete 永久删除]
     * @param  [type] $_name    [description]
     * @param  array  $parm     [description]
     * @param  [type] $callback [删除后，自定义回调函数]
     * @return [type]           [description]
     */
    protected function _foreverDelete($_name = null, $parm = array(), $callback) {

        //删除指定记录
        if (!$_name) $_name = $this->getControllerName();
        $model = D($_name);
        if (!empty($model)) {
            $pk = $model->getPk();
            if (empty($parm)) {
                $id = $_REQUEST[$pk];
                $parm = explode(",", $id);
            }
            else {
                if (is_string($parm)) {
                    $parm = explode(",", $parm);
                }
                else if (!is_array($parm)) {
                    return false;
                }
            }
            if (isset($parm) && !empty($parm)) {
                $condition = array($pk => array('IN', $parm));
                if (false !== $model->where($condition)->delete()) {

                    //处理回调函数
                    if ($callback && method_exists($this, $callback)) {
                        call_user_func_array(array($this, $callback), array($parm));
                    }
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }
    }

    private function getControllerName() {
        return CONTROLLER_NAME;
    }

    /*
     * 操作日志
    */

    protected function addlog($msg = null, $platform = null) {
        $date = date("Ym");
        $tablename = $this->prextable . "operation_log_" . $date;
        $create_sql = "CREATE TABLE IF NOT EXISTS {$tablename}(
                          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
                          `shop_id` int(11) COMMENT '门店ID',
                          `staff_id` int(11) DEFAULT NULL COMMENT '操作者ID',
                          `time` int(10) DEFAULT NULL COMMENT '操作时间',
                          `content` text COMMENT '操作内容',
                          `platform` varchar(20) DEFAULT NULL COMMENT '平台',
                          PRIMARY KEY (`id`)
                        )ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='操作日志信息表（一个月一张表，表名称wcz_operation_log_201501）'";
        $rs = $this->DC()->execute($create_sql);
        if ($rs === false) {
            return false;
        }
        if (empty($msg)) return false;
        $data = array('shop_id' => $this->shop_id, 'staff_id' => $this->staff_id, 'time' => time(), 'content' => $msg, 'platform' => $platform);
        $this->DC()->startTrans();
        $rs = $this->insert($data, "OperationLog_" . $date);
        if ($rs) {
            $this->DC()->commit();
            return true;
        }
        else {
            $this->DC()->rollback();
            return false;
        }
    }

    /**
     * 图片上传
     * @maxSize：图片大小（单位KB）,ext：允许图片扩展名,type:类型格式array('shop',1)表示图片保存在public下的Uploads/shop/1/文件夹下，autoSub自动保存子目录（如果为true则会自动生成日期目录）
     * @autoSub为true时，subName才有效,subName自定义规则生成子目录,格式array('date', 'Y-m-d')
     * @maes=array(array('egt',500),array('egt',400)),图片尺寸大小,egt:大于等于,gt大于,elt小于等于,lt小于
     * @thumb，生成缩略图，为空不生成,array(array(400,400),array(500,500))或array(array(400,400,2),array(500,500,1));
     * @thumb第三个值是缩放类型，1:标识缩略图等比例缩放类型 2:标识缩略图缩放后填充类型 3:标识缩略图居中裁剪类型 4:识缩略图左上角裁剪类型 5 ; 标识缩略图右下角裁剪类型 6 ;标识缩略图固定尺寸缩放类型
     * @other其他用户自定义参数
     */
    function uploadimg($type = null, $maxSize = null, $ext = null, $maes = null, $thumb = null, $other = array(), $autoSub = false, $subName = null) {

        $upload = new \Think\Upload();

        // 实例化上传类
        if (!empty($maxSize) && is_numeric($maxSize)) {
            $upload->maxSize = $maxSize * 1024;

            //B
        }
        if (is_string($ext)) {
            $ext = explode(",", $ext);
        }
        if (is_array($ext) && !empty($ext)) {
            $upload->exts = $ext;
        }
        if (is_array($maes) && !empty($maes)) {
            $upload->maes = $maes;
        }
        $upload->autoSub = $autoSub;
        if ($autoSub) {
            $upload->subName = $subName;
        }
        $root = $_SERVER['DOCUMENT_ROOT'];
        $upload->rootPath = $root . PUBLIC_PATH;
        if (!file_exists($upload->rootPath)) @mkdir($upload->rootPath, 0777, true);
        $a = "";
        if (!empty($type[0])) {
            $a.= $type[0] . "/";
        }
        if (!empty($type[1])) {
            $a.= $type[1] . "/";
        }
        $upload->savePath = 'Uploads/' . $a;
        $upload->savePath = substr($upload->savePath,0);
        // 设置附件上传目录
        if (!file_exists($upload->rootPath . $upload->savePath)) @mkdir($upload->rootPath . $upload->savePath, 0777, true);
        // 上传文件
        $info = $upload->upload();
        if (!$info) {

            // 上传错误提示错误信息
            $return = array('statusCode' => $upload->getErrorCode(), 'info' => $upload->getError(), 'other' => $other);
        }
        else {

            // 上传成功
            $thumb_url = $upload->rootPath . $upload->savePath . $upload->_getSaveName();
            $return_url = PUBLIC_PATH . $upload->savePath . $upload->_getSaveName();
            if (!empty($thumb) && is_array($thumb)) {
                $image = new \Think\Image();
                foreach ($thumb as $k => $v) {
                    if (is_array($v) && !empty($v)) {
                        if (empty($v[1])) $v[1] = $v[0];
                        if (empty($v[2])) $v[2] = 1;
                        if (!in_array($v[2], array(1, 2, 3, 4, 5, 6))) $v[2] = 1;
                        $image->open($thumb_url);
                        $image->thumb($v[0], $v[1], $v[2])->save($thumb_url . "_" . $v[0] . "x" . $v[1] . "." . pathinfo($upload->_getSaveName(), PATHINFO_EXTENSION));
                    }
                }
            }
            
            $return = array('statusCode' => 1, 'info' => slashto($return_url), 'other' => $other);
        }
        return $return;
    }

    /*
     * 获取图片地址
    */

    function getImage($imagename = null, $type = array(), $shop_id = 0) {
        if (empty($imagename)) return;

        $root = $_SERVER['DOCUMENT_ROOT'];
        $rootPath = $root . PUBLIC_PATH;
        $a = "";
        if (!empty($type[0])) {
            $a.= $type[0] . "/";
        }
        if (!empty($type[1])) {
            $a.= $type[1] . "/";
        }

        $savePath = $rootPath . 'Uploads/' . $a;
        $imagepath = $savePath . $imagename;
        $imagepath_1 = PUBLIC_PATH . 'Uploads/' . $a . $imagename;
        return array('realpath' => $imagepath, 'path' => $imagepath_1);
    }
}
?>