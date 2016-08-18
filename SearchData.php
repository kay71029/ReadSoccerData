<?php 
header("Content-Type:text/html; charset=utf-8");
class Crawler{

	protected $_curl = null;

    public function get_http_header($url)
    {
		if (is_null($this->_curl)) {
	    	$this->_curl = curl_init();
		}
		$curl = $this->_curl;
		

		//REF : http://www.111cn.net/phper/php/52113.htm
		curl_setopt($curl, CURLOPT_URL, $url);	
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER,1); //将头文件的信息作为数据流输出

		$content = curl_exec($curl);
		return str_replace('charset=MS950', 'charset=Utf-8', $content);
    }

    public function parserCookie($content){

		preg_match('/Set-Cookie:(.*);/i',$content,$str); //正则匹配
		$cookie = $str[1]; //获得COOKIE（SESSIONID）
	    return $cookie;
    }

    public function getSportData($url,$cookie){
		if (is_null($this->_curl)) {
	    	$this->_curl = curl_init();
		}
		$curl = $this->_curl;
		

		curl_setopt($curl, CURLOPT_URL, $url);	
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_COOKIE,$cookie);
	
		$content = curl_exec($curl);
		echo "<script>console.log(".$content.")</script>";  //this is for debug
		return str_replace('charset=MS950', 'charset=Utf-8', $content);
    }

    public function parserContent($input_lines){
    	//測試網站: http://www.phpliveregex.com/  
    	//preg_match_all("/parent.GameFT(.*);/", $input_lines, $output_array);  
    
  
        //更乾淨的資料
        $output_array = preg_grep("/parent.GameFT(.*);/", explode("\n", $input_lines));
    	sort($output_array);  //resort 的目的在於重新調整offset(index);

    	return $output_array;

    }


    public function parserArray($input_line){
        preg_match("/Array\((.*)\);/", $input_line, $output_array);

        $output = explode(",", $output_array[1]);     
        return $output;
    }

    public function main(){

    	//抓資料
    	$header  = $this->get_http_header('http://228365365.com/sports.php');
    	$cookie  = $this->parserCookie($header);
    	//顯示
    	echo $cookie;
    	//echo '<br/>';

    	$content = $this->getSportData("http://228365365.com/app/member/FT_browse/body_var.php?uid=test00&rtype=r&langx=zh-cn&mtype=3&page_no=0&league_id=&hot_game=und",$cookie);

    	//echo "<div>".$content."</div>";  //這樣會引發這個網站的 autoload 功能,會導致被轉page


    	//直接強制轉
    	$GameFT= $this->parserContent($content);
    	//顯示結果
    	//var_dump($GameFT);

        //拆開每一個字串 (逗點隔開)
        foreach ($GameFT as $key => $b){
        $arr= $this->parserArray($GameFT[$key]);
         var_dump($arr);
            
        }
        // var_dump($arr);

    }




}

   $crawler = new Crawler();
   $crawler -> main();

?>