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
/**
 * 文章逻辑
 */
class Article extends LogicBase
{
    
    /**
     * 文章分类编辑
     */
    public function articleCategoryEdit($data = [])
    {
        if($data){
    		$data['content'] = str_replace('/upload',getDoman().'/upload',$data['content']);
    	}
        $validate_result = $this->validateArticleCategory->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateArticleCategory->getError()];
        }
        
        $url = url('articleCategoryList');
        
        $result = $this->modelArticleCategory->setInfo($data);
        
        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '文章分类' . $handle_text . '，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '操作成功', $url] : [RESULT_ERROR, $this->modelArticleCategory->getError()];
    }
    
    /**
     * 获取文章列表
     */
    public function getArticleList($where = [], $field = 'a.*,m.nickname,c.name as category_name', $order = '')
    {
        
        $this->modelArticle->alias('a');
        
        $join = [
                    [SYS_DB_PREFIX . 'member m', 'a.member_id = m.id'],
                    [SYS_DB_PREFIX . 'article_category c', 'a.category_id = c.id'],
                ];
        
        $where['a.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        
        $this->modelArticle->join = $join;
        
        return $this->modelArticle->getList($where, $field, $order,20);
    }
    /**
     * 获取文章列表搜索条件
     */
    public function getWhere($data = [])
    {

        $where = [];
        
        !empty($data['search_data']) && $where['a.name|a.describe'] = ['like', '%'.$data['search_data'].'%'];

        !empty($data['category']) && $where['category_id'] = ['eq',end($data)];

        return $where;

        // if( isset($data[1]) && $data[1] == '.content'){
        //     return [];
        // }
        // if(isset($data['search_data'])){
        //     !empty($data['search_data']) && $where['a.name|a.describe'] = ['like', '%'.$data['search_data'].'%'];
        // }else{
        //     var_dump($data['category_id'][1]);exit;
        //     if( isset($data['category_id'][1]) && $data['category_id'][1] == '.content'){
        //         return [];
        //     }
        //     $where['category_id'] = ['eq',end($data)]; 
        // }
        // return $where;
    }
    
    /**
     * 文章信息编辑
     */
    public function articleEdit($data = [])
    {
    	
    	if($data){
    		$data['content'] = str_replace('/images',getDoman().'/images',$data['content']);
    		//zuoke_houtai.php/article
    		$data['content'] = str_replace('href=""','href="'.getDoman().'"',$data['content']);
    		
    		
    		
    	}
    	if(!empty($data['append'])){
    		//$data['content'] .= "<br/><a href='".getDoman().$data['append']."' download='".getDoman().$data['append']."'>下载附件</a>";
    		$data['content'] .= "<br/><a href='".getDoman().$data['append']."'>下载附件</a>";
    		unset($data['append']);
    	}
//		print_r($data);exit;
        $validate_result = $this->validateArticle->scene('edit')->check($data);
        
        if (!$validate_result) {
            
            return [RESULT_ERROR, $this->validateArticle->getError()];
        }
        
        $url = url('articleList');
        
        //如果描述为空 则获取内容的前160个字符

        if(empty($data['describe'])){
            $data['describe'] = cutstr_html($data['content'],160);
        }
        
        empty($data['id']) && $data['member_id'] = MEMBER_ID;

        $result = $this->modelArticle->setInfo($data);

        if(!empty($data['tag_name'])){
            //更新
            if($data['id']){
                ArticleTag($data['tag_name'],$data['id']);
            }
            //新增
            if(!empty($result)){
                ArticleTag($data['tag_name'],$result); 
            }            
            
        } 
        $result = !empty($data['id']) ? $data['id'] : $result;
        if(file_exists('./info/'.$result.'.html')){
        	
            @unlink('./info/'.$result.'.html');//删除静态文件 
//          echo $result."删除成功";
        }
        //详情更新
        $this->CreateHtmlCache('../public/info/',$result,getDoman().'/info/'.$result.'.html');
   
        $handle_text = empty($data['id']) ? '新增' : '编辑';
        
        $result && action_log($handle_text, '文章' . $handle_text . '，name：' . $data['name']);
        
        return $result ? [RESULT_SUCCESS, '文章操作成功', $url] : [RESULT_ERROR, $this->modelArticle->getError()];
    }

    /**
     * 获取文章信息
     */
    public function getArticleInfo($where = [], $field = 'a.*,m.nickname,c.name as category_name')
    {
        
        $this->modelArticle->alias('a');
        
        $join = [
                    [SYS_DB_PREFIX . 'member m', 'a.member_id = m.id'],
                    [SYS_DB_PREFIX . 'article_category c', 'a.category_id = c.id'],
                ];
        
        $where['a.' . DATA_STATUS_NAME] = ['neq', DATA_DELETE];
        
        $this->modelArticle->join = $join;
        
        return $this->modelArticle->getInfo($where, $field);
    }
    
    /**
     * 获取分类信息
     */
    public function getArticleCategoryInfo($where = [], $field = true)
    {
        
        return $this->modelArticleCategory->getInfo($where, $field);
    }
    
    /**
     * 获取文章分类列表
     */
    public function getArticleCategoryList($where = [], $field = true, $order = '', $paginate = 0)
    {
        
        return $this->modelArticleCategory->getList($where, $field, $order, $paginate);
    }
    
    /**
     * 文章分类删除
     */
    public function articleCategoryDel($where = [])
    {
        
        $result = $this->modelArticleCategory->deleteInfo($where);
        
        $result && action_log('删除', '文章分类删除，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '文章分类删除成功'] : [RESULT_ERROR, $this->modelArticleCategory->getError()];
    }
    
    /**
     * 文章删除
     */
    public function articleDel($where = [])
    {
        
        $result = $this->modelArticle->deleteInfo($where);
        
        $result && action_log('删除', '文章删除，where：' . http_build_query($where));
        
        return $result ? [RESULT_SUCCESS, '文章删除成功'] : [RESULT_ERROR, $this->modelArticle->getError()];
    }

    /**
     * 配置项
     */
    public function setConfig($data){

        $str = '';
        $key = [];
        $val = [];
        foreach ($data as $k => $v) {
            $str .= $k;
            array_push($key, $k);
            array_push($val, $v);
        }
        ltrim($str,',');
        $result = setconfig($key,$val);
        $result && action_log('配置项',$str.'配置成功');
        return $result ? [RESULT_SUCCESS,'配置成功'] : [RESULT_ERROR, '配置失败'];
    }
    /**
     * [CreateHtmlCache description]
     * @param [type] $root [description] 文件路径   
     * @param [type] $rule [description] 文件名称
     * @param [type] $url  [description] 远程地址
     */
    public function CreateHtmlCache($root,$rule,$url){
        ob_start();
        $ch = curl_init();
        $timeout = 5;
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $content = curl_exec($ch);
        curl_close($ch);
        
        ob_end_clean();
        $fileName = $root.$rule.'.html';
//      if(file_exists($fileName)){
//          @unlink($root.$rule.'.html');//删除静态文件 
//      }
        if($fp = fopen($fileName, 'w+')){//创建一个文件，并打开，准备写入
            $startTime = microtime();
            do{
                $canWrite = flock($fp, LOCK_EX); //文件锁
            }while((!$canWrite)&&((microtime()-$startTime)<1000));
            if($canWrite){
                
                fwrite($fp, $content)or die('写文件错误');//把php页面的内容全部写入html
            
            }
            fclose($fp);
        }

        return [RESULT_SUCCESS, '静态化成功'];
    }

    public function CreateHtmlCacheAll($arr = []){

        ob_start();
        $ch = curl_init();
        $timeout = 5;
        curl_setopt ($ch, CURLOPT_URL, $arr[2]);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $content = curl_exec($ch);

        curl_close($ch);
        ob_end_clean();
        $fileName = $arr[0].$arr[1].'.html';
        if(file_exists($fileName)){
            @unlink($arr[0].$arr[1].'.html');//删除静态文件 
        }
        echo $fileName;
        print_r($content);exit;
        if(file_put_contents($fileName,$content)){
            return true;
        }else{
            return false;
        }   
        // if($fp = fopen($fileName, 'w+')){//创建一个文件，并打开，准备写入
        //     $startTime = microtime();
        //     do{
        //         $canWrite = flock($fp, LOCK_EX); //文件锁
        //     }while((!$canWrite)&&((microtime()-$startTime)<1000));
        //     if($canWrite){
                
        //         fwrite($fp, $content)or die('写文件错误');//把php页面的内容全部写入html
            
        //     }
        //     fclose($fp);
        // }
        
    }

}
