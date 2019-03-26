<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\common\logic;

class Refer extends LogicBase
{
    
    public function getReferList($where = [],$field="*",$order='id desc'){

        return $this->modelRefer->getList($where, $field, $order);

    }

    /**
     * 获取列表搜索条件
     */
    public function getWhere($data = [])
    {
        
        $where = [];
        
        !empty($data['search_data']) && $where['a.name|a.describe'] = ['like', '%'.$data['search_data'].'%'];
        
        return $where;
    }

}
