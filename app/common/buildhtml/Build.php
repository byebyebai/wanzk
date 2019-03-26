<?php
namespace app\common\buildhtml;

class Build{

	/**
	 * 生成静态页 类库
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
		if(file_exists($fileName)){
			@unlink($root.$rule.'.html');//删除静态文件 
		}
		if($fp = fopen($fileName, 'w')){//创建一个文件，并打开，准备写入
			$startTime = microtime();
			do{
				$canWrite = flock($fp, LOCK_EX); //文件锁
			}while((!$canWrite)&&((microtime()-$startTime)<1000));
			if($canWrite){
				
				fwrite($fp, $content)or die('写文件错误');//把php页面的内容全部写入html，然后……
			
			}
			fclose($fp);
		}

    }
}