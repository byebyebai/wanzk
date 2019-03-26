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

namespace app\admin\controller;
use think\Db;
use app\common\buildhtml\Build;
use app\common\logic\File as LogicFile;
use think\Cache;
/**
 * 文章控制器
 */
class Article extends AdminBase
{

    private $category_arr = [];
    /**
     * 文章列表
     */
    public function articleList()
    {
        $where = [];
        if($this->param){
            $where = $this->logicArticle->getWhere($this->param);
        } 

        //print_r($this->logicArticle->getArticleList($where, 'a.*,m.nickname,c.name as category_name', 'a.sort,a.create_time desc'));
        $category = getCategory(['pid'=>0],'','*'); 
        $this->assign('list', $this->logicArticle->getArticleList($where, 'a.*,m.nickname,c.name as category_name','a.sort,a.create_time desc'));
        return $this->fetch('article_list',[
            'cotegory'=>$category
        ]);
    }
    
    /**
     * 文章添加
     */
    public function articleAdd()
    {
        
        $this->articleCommon(1);
        
        return $this->fetch('article_edit');
    }
    

    private function getPid($id){

        $where['id'] = ['eq',$id];
        $info = Db::name('article_category')->where('id',$id)->field('id,pid,name')->find();
        if($info){
            array_push($this->category_arr, $info);
            if($info['pid'] == 0){
                return false;
            }
            $this->getPid($info['pid']);
        } 

    }

    /**
     * 文章编辑
     */
    public function articleEdit()
    {
        
        $this->articleCommon(2);
        
        $info = $this->logicArticle->getArticleInfo(['a.id' => $this->param['id']], 'a.*,m.nickname,c.name as category_name');
        $res = $this->getPid($info['category_id']);

        if(false !== $this->getPid($info['category_id'])){
            //组装当前所在栏目
            $count = count($this->category_arr)-1;
            $category = [];
            for($i=$count;$i>=0;$i--){
                $category[$i] = $this->category_arr[$i];
            }
        
        }else{
            $count = count($this->category_arr)-1;
            $category = [];
            for($i=$count;$i>=0;$i--){
                $category[$i] = $this->category_arr[$i];
            }
        }
        $this->assign('info', $info);
        
        return $this->fetch('article_edit',['category'=>$category]);
    }
    
    /**
     * 文章添加与编辑通用方法
     */
    public function articleCommon($status)
    {

        IS_POST && $this->jump($this->logicArticle->articleEdit($this->param));

        if($status == 1){
            $this->assign('article_category_list', $this->logicArticle->getArticleCategoryList(['pid'=>0], 'id,name,pid', '', false));
        }else{
            $this->assign('article_category_list', $this->logicArticle->getArticleCategoryList([], 'id,name,pid', '', false));
        }
        
    }

    
    /**
     * 文章分类添加
     */
    public function articleCategoryAdd()
    {
            
        IS_POST && $this->jump($this->logicArticle->articleCategoryEdit($this->param));
        $this->assign('article_category_list', $this->logicArticle->getArticleCategoryList([], 'id,name', '', false));
        return $this->fetch('article_category_edit');
    }
    /**设置排序
     * [setSort description]
     */
    public function setSort()
    {
        
        $this->jump($this->logicAdminBase->setSort('article', $this->param));
    }
    
    public function setCategorySort()
    {
        
        $this->jump($this->logicAdminBase->setSort('article_category', $this->param));
    }
    /**
     * 文章分类编辑
     */
    public function articleCategoryEdit()
    {
        
        IS_POST && $this->jump($this->logicArticle->articleCategoryEdit($this->param));
        
        $info = $this->logicArticle->getArticleCategoryInfo(['id' => $this->param['id']]);

        $this->assign('info', $info);
        $this->assign('article_category_list', $this->logicArticle->getArticleCategoryList(['pid'=>0], 'id,name', '', false));
        return $this->fetch('article_category_edit');
    }
    
    /**
     * 文章分类列表
     */
    public function articleCategoryList()
    {
        $where = empty($this->param['pid']) ? ['pid' => 0] : ['pid' => $this->param['pid']];
//      $list = $this->logicArticle->getArticleCategoryList($where,'','sort asc','');
        $list = db::name('article_category')->where($where)->order('sort asc')->select();
        foreach ($list as $k => $v) {
            if($v['pid'] == 0){
                //获取对应的子分类
                $childCategory = getCategory(['pid'=>$v['id']],'','');
                $list[$k]['childCategory'] = $childCategory;
            }
            
        }

        $this->assign('list', $list); 
        return $this->fetch('article_category_list');
    }
    
    public function setCategoryIsShow(){

    	if($this->param){
			$result = db::name('article_category')->where('id',$this->param['cid'])->update(['isShow'=>$this->param['id']]);    		
    		if($result){
    			return json(['status'=>1]);
    		}else{
    			return json(['status'=>0]);
    			
    		}
    	}
    }

    public function getChildCategory(){
        $param = $this->param;
        $list = getCategory(['pid'=>$param['id']],'','');
        return json(['data'=>$list]);
    }
    //根据父级id查询子id
    public function getCategoryCid(){
        if(IS_POST){
            $category_list = $this->logicArticle->getArticleCategoryList(['pid'=>$this->param['id']], 'id,name', '', false);
            $list = [];
            foreach ($category_list as $k => $v) {
                $list[$k]['id'] = $v['id'];
                $list[$k]['name'] = $v['name'];
            }
            if(!empty($list)){
                return $this->api_json(1,'获取成功',$list);
            }else{
                return $this->api_json(0,'没有分类了');
            }
        }
    }
    /**
     * 文章分类删除
     */
    public function articleCategoryDel($id = 0)
    {
        
        $this->jump($this->logicArticle->articleCategoryDel(['id' => $id]));
    }
    
    /**
     * 数据状态设置
     */
    public function setStatus()
    {
        
        $this->jump($this->logicAdminBase->setStatus('Article', $this->param));
    }
    //附件添加
	public function uploadFile(){

		$result = get_sington_object('fileLogic', LogicFile::class)->fileUpload('file');
        
        $data  = false === $result ? [RESULT_ERROR => DATA_NORMAL, RESULT_MESSAGE => '文件上传失败'] : [RESULT_ERROR => DATA_DISABLE, RESULT_URL => get_file_url($result['id'])];

        return throw_response_exception($data);
	}
    /**
     * [testTime description] 考试时间设置
     * @return [type] [description]
     */
    public function testTime(){
        $info['test_time'] = config('test_time');
        $info['registration_time'] = config('registration_time');
        $param = $this->param;
        if($param){
            
            //$this->jump($this->logicArticle->setConfig($param));
            
        }
        $this->assign('info',$info);
        return $this->fetch('testtime');
    }
    private $cacheArray = [];
    private $num = 0;

    /**
     * [static_all description] 生成静态文件
     * @return [type] [description]
     */
    public function static_all(){

        $list = getArctile(['status'=>1],'','id','');

        $cacheArray = [];
        //更新文章  info
        foreach ($list as $k => $v) {

            array_push($cacheArray, ['../public/info/',$v['id'],getDoman().'/info/'.$v['id'].'.html']);
        }

        $this->cacheArray = $cacheArray;

        $this->doStaticPage();

    }
    /**
     * [doStaticPage description]处理静态化
     * @return [type] [description]
     */
    public function doStaticPage(){

        if($this->num<count($this->cacheArray)){

            //静态化页面
            $result = $this->setHtmlCache($this->cacheArray[$this->num]);

            if($result){
                $this->num++;
                usleep(10);
                $this->doStaticPage();
            }else{
                echo $this->cacheArray[$this->num]."-静态化失败，请与开发者联系";
            }   
        }else{
            return $this->success('静态化成成功',url('article/articlelist'));
        }

    }

    public function setHtmlCache($arr = []){

        //检测是否生成过静态文件 
        if (file_exists($arr[0].$arr[1].".html")){//已经存在静态文件 
            @unlink($arr[0].$arr[1].".html");//删除静态文件 
            return file_put_contents($arr[0].$arr[1].".html", file_get_contents($arr[2]));//重新生成 
        }else{//不存在静态文件直接生成
            return file_put_contents($arr[0].$arr[1].".html", file_get_contents($arr[2])); 
        }
    }
    /**
     * [banner description] 轮播设置
     * @return [type] [description]
     */
    public function banner(){
        $list = $this->logicBanner->getBannerList(['status'=>1],'','');
        //print_r($list);exit;
        return $this->fetch('',['list'=>$list]);

    }
    public function banner_add(){
    	
        $this->bannerCommon();
   
        return $this->fetch('banner_edit');

    }

    public function banner_edit(){

        $this->bannerCommon();
        return $this->fetch('banner_edit',['info'=>$this->logicBanner->getBannerInfo(['id'=>$this->param['id']])]);
    }
    public function bannerCommon(){
        IS_POST && $this->jump($this->logicBanner->bannerAdd($this->param));
    }

    public function setBannerStatus(){
        $this->jump($this->logicAdminBase->setStatus('Banner', $this->param));
    }

    public function getProvince(){
        $list = getProvince();
        return json($list);
    }
    public function getCity(){
        if(IS_POST){
            $pid = $this->param['pid'];
            $list = getCity($pid);
            return json($list);
        } 
        
    }
    public function register(){
        $where = [];
        if($this->param){
            !empty($this->param['search_data']) && $where['name|tel'] = ['like', '%'.$this->param['search_data'].'%'];
        }
        $list = Db::name('register')->order('create_time desc')->where($where)->paginate(13);
        // print_r($list);exit;
        return $this->fetch('register',['list'=>$list]);
    }
    
    public function registerDel($ids = 0){
    	
    	if($ids){
    		$result = Db::name('register')->where('id',$ids)->delete();
    		$res = $result ? [RESULT_SUCCESS, '删除成功'] : [RESULT_ERROR, '删除失败'];
    	}else{
    		$res =  [RESULT_ERROR, '删除失败'];
    	}
    	$this->jump($res);
    	
    }
    
    public function lanmuStatic(){
    	
    	if(!IS_POST){
    		return json(['msg'=>'请求非法']);
    	}
    	
    	$postIds = $this->param;

    	$level1Ids = ''; 
    	foreach($postIds['checkID'] as $k => $v){
    		$level1Ids .= $v .',';
    	}
    	$level1Ids = rtrim($level1Ids,',');
    	$level1Array = $this->getArticleCategoryByWhere('id','in',$level1Ids);
		$level2Array = $this->getArticleCategoryByWhere('pid','in',$level1Ids);
		$list = array_merge($level1Array,$level2Array);
    	$categoryIds = '';
    	foreach($list as $k => $v){
    		if($v['id'] == 6 || $v['id'] ==8){
    			continue;
    		}
    		$categoryIds .= $v['id'].',';
    	}

    	$categoryIds = rtrim($categoryIds,',');
		$sql = "SELECT category_id,COUNT(*) as count FROM `zk_article` WHERE `category_id` IN ({$categoryIds}) GROUP BY category_id";
		$result = Db::query($sql);
		$urls = []; //要更新的栏目集合
		$pageNum = 14;
		foreach($result as $k => $v){
			$nums = ceil($v['count']/$pageNum);
			if($nums>1){
				for($i=2;$i<=$nums;++$i){

					$url = config("app_host").'/lanmu/'.$v['category_id'].'.html?page='.$i;
					array_push($urls,$url);
				}
			}
				
			$url = config("app_host").'/lanmu/'.$v['category_id'].'.html';
			array_push($urls,$url);
				
			

		}
		Cache::set('lanmu',$urls);
		return json(['status'=>1]);
   }
    public function doLanmuStatic(){
    	
    	if(!IS_POST){
    		return json(['msg'=>'请求非法']);
    	}
    	$index = $this->param['index'];
    	$list = Cache::get('lanmu');
    	if(!empty($list)){

    		if($index>count($list)-1){
    			Cache::rm('lanmu'); 
    			return json(['status'=>1,'msg'=>'更新完毕！']);
    		}else{
    			return json(['status'=>2,'msg'=>'更新第'.$index."条",'url'=>$list[$index],'index'=>++$index]);
    		}
    		
    		
    	}else{
    		return json(['status'=>1,'msg'=>'更新完毕！']);
    	}

    }
    
    protected function getArticleCategoryByWhere($field,$where,$ids){
    	return db::name('article_category')->where($field,$where,$ids)->field('id,pid')->select();
    }
}
