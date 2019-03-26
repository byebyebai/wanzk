<?php

namespace app\admin\controller;
use think\Db;
/**
 * 首页控制器
 */
class Refer extends AdminBase
{
    
    /**
     * 首页方法
     */
    public function listRefer(){
        $where = [];
        if($this->param){
            !empty($this->param['search_data']) && $where['name|tel'] = ['like', '%'.$this->param['search_data'].'%'];
        }
        $list = Db::name('refer')->order('create_time desc')->where($where)->paginate(13);
        return $this->fetch('',['list'=>$list]);
    }

    public function setStatus()
    {

        if(Db::name('refer')->where('id',$this->param['ids'])->delete()){
            $this->jump([RESULT_SUCCESS, '操作成功', url('listRefer')]);
        }
      
    }
}
