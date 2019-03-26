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

namespace app\index\controller;
use think\Db;
use think\Request;
/**
 * 前端首页控制器
 */
class Index extends IndexBase
{

    // 首页
    public function index()
    {
    	

        //获取热点排行前7的数据
        $hotArtile = getArctile(['status'=>1],'hits desc','seo_title,name,id,create_time',7);

        //获取最新的7条数据
        $newArtile = getArctile(['status'=>1],'create_time desc','seo_title,create_time,name,id',7);
//		print_r($hotArtile);
        //获取报考学校 id 26
        $schoolList = getCategory(['status'=>1,'pid'=>6]);
	    	
        //获取自考专业 id 27
        $major = getArctile(['status'=>1,'category_id'=>27],'sort asc,id desc','id,cover_id,name',5);
        //友情链接
        $links = Db::name('blogroll')->where('status',1)->order('sort asc')->select();
        //轮播
        $banner = Db::name('banner')->where('status',1)->select();

        return $this->fetch('',[
            'hot'=>$hotArtile,
            'new'=>$newArtile,
            'school'=>$schoolList,
            'major' => $major,
            'links' =>$links,
            'banner'=>$banner
        ]);
    }

    /**
     * [info description] 详细内容
     * @return [type] [description]
     */
    public function info(){

        $id = $this->request->param('id');

        $where = [];
        
        !empty((int)$id) && $where['a.id'] = $id;
        
        $data = $this->logicArticle->getArticleInfo($where);

        // print_r($data);
        //上一页 下一页
        $front=Db::name('article')->where('id','<',$id)->order(['id'=>'desc'])->field('id,name')->find();

        $after=Db::name('article')->where('id','>',$id)->order(['id'=>'asc'])->field('id,name')->find();

        $front=empty($front)?'没有了':$front;
        $after=empty($after)?'没有了':$after;
        //推荐文章
        $recommendWhere['category_id'] = ['eq',$data['category_id']];
        $recommendWhere['id'] = ['neq',$data['id']];
        $randList = random_data(10,'article',$recommendWhere);

        //详情热门排行 最新动态
        $hotArtile = getArctile(['status'=>1],'hits desc','name,id,create_time',9);

        //获取最新的9条数据
        $newArtile = getArctile(['status'=>1],'create_time desc','create_time,name,id',9);
        // $data = strip_tags($data['describe']);
        // print_r($data);exit;
        $this->assign('front',$front);
        $this->assign('after',$after);
        $this->assign('rand',$randList);
        $this->assign('article_info',$data);
        $this->assign('hot',$hotArtile);
        $this->assign('new',$newArtile);
        if($data['category_id'] == 8){

            //获取对应专业信息
            $zhuanye_info = getArctile(['id'=>$id]);

            return $this->fetch('zhuanye_info',['zhuanye'=>$zhuanye_info[0]]);
        }else{
            return $this->fetch('info');
        }
        
    }
    public function hits(){

        $id = $this->request->param('id');
        $hits = Db::name('article')->where('id',$id)->value('hits');
        Db::name('article')->where('id',$id)->setInc('hits');
        return json(['hits'=>$hits+1]);
    }
    /**
     * [category_list description] 二级分类
     * @return [type] [description]
     */
    public function lanmu(){

        $where = [];
        $cid = $this->request->param('id');
        !empty((int)$cid) && $where['a.category_id'] = $cid;
        
 
        
        //获取分类
        $category = $this->logicArticle->getArticleCategoryList(['id'=>$cid,'isShow'=>1], true, 'sort asc,create_time desc', false);
        
        $this->assign('category',$category);
        //获取该分类下面的子分类 
        $childCate = $this->logicArticle->getArticleCategoryList(['pid'=>$category[0]['id'],'isShow'=>1], true, 'sort asc,create_time desc', false);
		
        switch ($category[0]['template']) {
        	
            case '1'://普通一级目录
        		
        		$parent_category = getCategory(['id'=>$category[0]['pid']]);
                $list = db('article')->alias('a')->join("member b",'a.member_id =b.id')->where($where)->order('a.sort desc ,a.add_time desc')->field('a.id,a.name,a.content,a.add_time,a.describe,b.nickname')->paginate(14);
				
                return $this->fetch('list_2',[
                    'article_list'=>$list,
                    'parent_category'=>$parent_category
                ]);
                
                break;
            case '2': //多分类 模板 
                $list = [];
                foreach ($childCate as $k => $v) {
                    $list[$k]['category_name'] = $v['name'];
                    $list[$k]['id'] = $v['id'];
                    $list[$k]['seo_name'] = $v['seo_name'];
                    $list[$k]['cover_id'] = $v['cover_id'];
                    $list[$k]['item'] = $this->logicArticle->getArticleList(['a.category_id'=>$v['id']], 'a.*,m.nickname,c.name as category_name', 'a.add_time desc,id desc,a.sort desc');
                }   
//				print_r($list);
                return $this->fetch('list_1',[
                    'list'=>$list,
                    'category'=>$category,
                    'childCate'=>$childCate
                ]);
                  
                break;
            case '3': //学校模板
                $list = [];
                foreach ($childCate as $k => $v) {
                    $list[$k]['category_name'] = $v['name'];
                    $list[$k]['id'] = $v['id'];
                    $list[$k]['cover_id'] = $v['cover_id'];
                    $list[$k]['item'] = $this->logicArticle->getArticleList(['a.category_id'=>$v['id']], 'a.*,m.nickname,c.name as category_name', 'a.hits desc');
                }
                return $this->fetch('list_3',[
                    'list'=>$list,
                    'category'=>$category
                ]);
                break;    
            default:
                # code...
                break;
        }

    }


    public function tag(){
        $param = $this->request->param();

        if($param){
            if(!empty($param['cate_id'])){
            	
            	
                $category = $this->logicArticle->getArticleCategoryList(['id'=>$param['cate_id']], true, 'create_time asc', false);
                $this->assign('category',$category);

                if($param['id'] == 3){
            		$list = db('article')->where(['category_id'=>8,'status'=>1])->order("add_time desc")->field('id,name,add_time,describe,category_id')->paginate(11);
//          		print_r($list);exit;
            	}else{
            		$tag_info = Db::name('tag')->where('tag_id',$param['id'])->find();
	                $where['status'] = ['eq',1];
	
	                $where['id'] = ['in',ltrim($tag_info['aid'],' ,')];
	
	                $where['name'] = ['like',"%".$category[0]['name']."%"];
					    		
	                $list = db('article')->where($where)->field('id,name,add_time,describe,category_id')->paginate(11);
            	}
                $this->assign('list', $list);
                
                if(in_array($param['id'],[2,3])){
                   return $this->fetch('tag_'.$param['id']);
                }
            }else{
                $where['tag_id'] = $param['id'];
                $tagInfo = db('tag')->where($where)->find();
                
                $list = db('article')->where('id','in',$tagInfo['aid'])->order("add_time desc")->field('id,name,add_time,describe')->paginate(13);
                // print_r($list);exit;
                return $this->fetch('',[
                    'tagInfo'=>$tagInfo,
                    'list'=>$list
                ]);
            }
            
           
        }
    }

    public function zhuanye(){
        if($this->param){
            $data = $this->param;
            $keywords = [];
            $i = 0;
            foreach ($data as $k => $v) {
                $keywords[$i] = $v;
                $i++;
            }
            if(count($keywords)<2){
                if($keywords[0] != "全部"){
                    $whereSql = "AND zhuanye_level like '%".$keywords[0]."%'";
                }else{
                    $whereSql = "";
                }
            }else{

                if($keywords[1] == "全部" && $keywords[0] == "全部"){
					$whereSql = '';
                }else{
                    if($keywords[0] == "全部" && $keywords[1] != "全部"){
                    	
                        $whereSql = "AND subject like '%".$keywords[1]."%'";
                        
                    }else if($keywords[1] == "全部" && $keywords[0] != "全部"){
                    	
                        $whereSql = "AND zhuanye_level like '%".$keywords[0]."%'";
                        
                    }else{
                    	
                        $whereSql = "AND zhuanye_level like '%".$keywords[0]."%' AND subject like '%".$keywords[1]."%'";
                        
                    }
                    
                }  
                     
            }
			
            $list = Db::query("SELECT * FROM zk_article WHERE category_id=8 ".$whereSql);
			
            
        }else{
            //获取全部专业
            $list = getArctile(['status'=>1,'category_id'=>8],'sort,create_time desc','name,id,create_time,category_id,cover_id','');
        }
//		foreach($list as $k =>$v){
//			$list[$k]['name']=str_replace('安徽自考','',$v['name']); 
//		}
        return $this->fetch('',['list'=>$list]);
    }

    public function school(){
        //获取安徽地区列表
        $address_list = Db::name('district')->where('upid',12)->select();
        if($this->param){
            $data = $this->param;

            if(count($data)>1){
                if($data['keywords'] == '全部' && $data['address'] != '0'){

                    $whereSql = " WHERE pid=6 and  shi = ".$data['address'];

                }else if($data['keywords'] != '全部' && $data['address'] == 0){

                    $whereSql = " WHERE pid=6 and  level like '%".$data['keywords']."%'";

                }else{
                    if($data['keywords'] == '全部' && $data['address'] == 0){
                        $whereSql = " WHERE status=1 and pid=6";
                    }else{
                        $whereSql = " WHERE pid=6 and level like '%".$data['keywords']."%' and shi=".$data['address'];
                    }

                }
            }else{
                if(!empty($data['keywords']) && $data['keywords'] != '全部'){

                    $whereSql = " WHERE pid=6 and level like '%".$data['keywords']."%'"; 

                }elseif(!empty($data['address']) && $data['address'] != '0'){

                    $whereSql = " WHERE pid=6 and shi =".$data['address'];  

                }else{
                    $whereSql = " WHERE status=1 and pid=6";
                }
                
                     
            }

            $list = Db::query("SELECT * FROM zk_article_category".$whereSql);
        }else{
            //获取学校
            // $list =  getCategory();
            $list = db('article_category')->where(['status'=>1,'pid'=>6])->order('sort,create_time desc')->select();

        }
          
        return $this->fetch('',['list'=>$list,'address_list'=>$address_list]);
    }

    public function search(){
        $param = $this->request->param();

        $where = [];
        if(!empty($param['keyword'])){
            $param['keyword'] = unicode_decode($param['keyword']);
            $where['status'] = ['eq',1];
            $where['name'] = ['like',"%".$param['keyword']."%"];
        }

        $list = db('article')->where($where)->field('id,name,add_time,cover_id,describe')->order('create_time desc')->paginate(14,false,[
        	'query' => $param,
        ]);

        $data = $list->all();
        
        if(!empty($param['keyword'])){
            foreach ($data as $k => $v) {

                $data[$k]['name'] = str_replace($param['keyword'], "<span style='color:red;'>{$param['keyword']}</span>", $v['name']);
                
            }

        }

        // foreach ($data as $k => $v) {
        //     $data[$k]['name'] = str_replace($param['keyword'], "<span style='color:red;'>{$param['keyword']}</span>", $v['name']);
        // } 

        return $this->fetch('',[
            'list'=>$list,
            'data'=>$data
        ]);
    }

    public function indexRefer(){
        $param = $this->request->param();

        $data = $param['jsonData'];

        if($data){
            // print_r($data);
            $data['create_time'] = time();
            $res = Db::name('refer')->insert($data);
            if($res){
                //echo "<script>alert('提交成功')</script>";exit;
                return $this->apiReturn(1,'提交成功',$res);
            }else{
                //echo "<script>alert('数据发生错误')</script>";exit;
                return $this->apiReturn(0,'数据发生错误');
            }
        }
    }

    public function sitemap(){

        $article_list = db('article')->where('status',1)->order('id desc')->field('id,name')->select();
        //$list = db('article_category')->alias('a')->join(' article b','a.id = b.category_id')->field('a.id,a.pid,a.seo_name,b.id as aid,b.name')->select();
        $category_list = db('article_category')->where('isShow',1)->order('id asc')->field('id,seo_name,pid,name')->select();
        $list = list_to_tree($category_list);

        $tag_list = db('tag')->field('tag_id,tag_name,aid')->select();
        foreach ($tag_list as $k => $v) {
            $tag_list[$k]['count'] = count(explode(',', $v['aid']));
        }

        return $this->fetch('',[
            'article_list'=>$article_list,
            'category_list'=>$list,
            'tag_list'=>$tag_list
        ]);
    }
    
    public function registration_time(){
    	
	    $data = [];
	    
	    $time = strtotime(getConfig('registration_time'));
	    $data['date'] = date('m',$time).'月'.date('d',$time).'日';
	    $data['day'] = floor(($time-time())/3600/24);
	    return json($data);
	}
	/**
	 * [getTestTime description]获取考试时间
	 * @return [type] [description]
	 */
	public function getTestTime(){
	    $data = [];
	    $time = strtotime(getConfig('test_time'));
	    $data['date1'] = date('m',$time).'月'.date('d',$time).'日';
	    $data['day1'] = floor(($time-time())/3600/24);
	    return json($data);
	}
	
	public function static_lanmu(){
		
		$url = $this->request->param()['surl'];

		$html = file_get_contents($url);

		$preg = '/lanmu\/([0-9]*)\.html\?page=([0-9]*)/i';
		//匹配内容中对应的分页参数替换
		preg_match_all($preg,$html,$match);
		$url_replace = '';
		for($i=0;$i<count($match);++$i){
		    for($j=0;$j<count($match[$i]);++$j){
		        if($match[2][$j] == 1){
		            $url_replace = 'lanmu/'.$match[1][$j].'.html';
		        }else{
		            $url_replace = 'lanmu/'.$match[1][$j].'_'.$match[2][$j].'.html';
		        }
		        $html = str_replace($match[0][$j],$url_replace,$html);
		    }
		}
		
		$path = './lanmu/';
		
		//匹配访问文件判断写入目录
		preg_match($preg,$url,$urlInfo);
		if($urlInfo){
			if($urlInfo[2] == 1){
				$filename = $path.$urlInfo[1].'.html';
			}else{
				$filename = $path.$urlInfo[1].'_'.$urlInfo[2].'.html';
			}
			
		}else{
			$preg = '/lanmu\/(.*)\.html/i';
			preg_match($preg,$url,$urlInfo);
			$filename = $path.$urlInfo[1].'.html';
		}
		
		if(!file_exists($path)){
			 mkdir($path);
		}
		file_put_contents($filename,$html);
		return json(['status'=>1,'msg'=>$filename.'更新成功！']);
	}

}
