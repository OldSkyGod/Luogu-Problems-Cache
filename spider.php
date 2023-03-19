<?php
function get($url){
    sleep(1);
    echo $url."\n";
    while(($res = file_get_contents($url)) == False);
    return $res;
}
$prefix = "problem/";
$handler = fopen("problems_list.md", "w");
$home = get("https://www.luogu.com.cn/problem/list?_contentOnly=1");
$home_arr = json_decode($home, true);
$page_total = ceil($home_arr['currentData']['problems']['count'] / $home_arr['currentData']['problems']['perPage']);
for($i = 1; $i <= $page_total; $i++){
    $content = get("https://www.luogu.com.cn/problem/list?page=$i&_contentOnly=1");
    $content_arr = json_decode($content, true);
    foreach($content_arr['currentData']['problems']['result'] as $index => $key){
        $id = $content_arr['currentData']['problems']['result'][$index]['pid'];
        $name = $content_arr['currentData']['problems']['result'][$index]['title'];
        echo $id." ".$name."\n";
        fwrite($handler, "- [$id $name](https://www.luogu.com.cn/problem/$id)\n");
        if(!file_exists($prefix.$id.".md")){
            $pro = get("https://www.luogu.com.cn/problem/$id?_contentOnly=1");
            $pro_arr = json_decode($pro, true);
            $background = $pro_arr['currentData']['problem']['background'];
            $description = $pro_arr['currentData']['problem']['description'];
            $hint = $pro_arr['currentData']['problem']['hint'];
            $input_format = $pro_arr['currentData']['problem']['inputFormat'];
            $output_format = $pro_arr['currentData']['problem']['outputFormat'];
            $samples = $pro_arr['currentData']['problem']['samples'];
            $md = fopen($prefix.$id.".md", "w");
            fwrite($md, "# $name\n\n## 题目背景\n\n$background\n\n## 题目描述\n\n$description\n\n## 输入格式\n\n$input_format\n\n## 输出格式\n\n$output_format\n\n");
            foreach($samples as $i => $v){
                $sample = $samples[$i];
                fwrite($md, "## 样例 #$i\n\n");
                foreach($sample as $ii => $vv){
                    $input = $sample[$ii][0];
                    $output = $sample[$ii][1];
                    fwrite($md, "### 样例输入 #$i\n```\n$input\n```\n\n### 样例输出 #$i\n\n```\n$output\n```\n\n");
                }
            }
            fwrite($md, "## 提示\n\n$hint\n");
            fclose($md);
        }
    }
}
fclose($handler);
?>
