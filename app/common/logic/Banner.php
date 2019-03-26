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


class Banner extends LogicBase
{
	/**
	 * [bannerAdd description] 添加首页轮播
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
    public function bannerAdd($data = []){
        
    	$url = url('banner');
        if(empty($data['id'])){
            $data['status'] = 1;
            $data['create_time'] = time();
        }
    	$result = $this->modelBanner->setInfo($data);

    	$handle_text = empty($data['id']) ? '新增' : '编辑';

    	$result && action_log($handle_text, '首页轮播' . $handle_text . '，图片标题：' . $data['title']);

        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelBanner->getError()];
    }

    public function getBannerInfo($where = [], $field = true)
    {
        
        return $this->modelBanner->getInfo($where, $field);

    }

    public function getBannerList($where = [], $field = true, $order = '', $paginate = 0)
    {

        return $this->modelBanner->getList($where, $field, $order, $paginate);

    }
}
