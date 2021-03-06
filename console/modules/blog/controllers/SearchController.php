<?php

namespace console\modules\blog\controllers;

use blog\components\UrlService;
use common\models\library\Book;
use common\models\posts\Posts;
use common\models\search\IndexSearch;
use common\service\GlobalUrlService;
use console\modules\blog\Blog;

class SearchController extends Blog{

    public function actionBuild(){
        $batch_data = [];
        $date_now = date("Y-m-d H:i:s");
        $this->echoLog("=========start build({$date_now})==========");
        /*build blog*/
        $post_list = Posts::find()->where(['status' => 1])->orderBy("id asc")->all();
        if($post_list){
            foreach( $post_list as $_post_info ){
                $tmp_search_key = $_post_info['title']."#@#".$_post_info['tags'];
                $batch_data[] = [
                    "title" => $_post_info['title'],
                    "description" => strip_tags($_post_info['content']),
                    "book_id" => 0,
                    "post_id" => $_post_info['id'],
                    "search_key" => $tmp_search_key,
                    "image" => $_post_info['image_url']
                ];
            }
        }

        $book_list = Book::find()->where(['status' => 1])->orderBy("id asc")->all();
        if($book_list){
            foreach($book_list as $_book_info){
                $tmp_search_key = $_book_info['subtitle']."#@#".$_book_info['tags'];
                $batch_data[] = [
                    "title" => $_book_info['subtitle'],
                    "description" => $_book_info['summary'],
                    "book_id" => $_book_info['id'],
                    "post_id" => 0,
                    "search_key" => $tmp_search_key,
                    "image" => GlobalUrlService::buildPic1Static($_book_info['image_url'],['w'=>600])
                ];
            }
        }

        if($batch_data && count($batch_data) > 0){//批量插入速度快
            foreach($batch_data as $_item){
                if( !$_item['post_id'] && !$_item['book_id'] ){
                    $this->echoLog("skip");
                    continue;
                }
                $query = IndexSearch::find();
                if( $_item['post_id'] ){
                    $query->where(['post_id' => $_item['post_id']]);
                }else{
                    $query->where(['book_id' => $_item['book_id']]);
                }
                $tmp_info = $query->one();
                if($tmp_info){
                    $model_index_search = $tmp_info;
                }else{
                    $model_index_search = new IndexSearch();
                    $model_index_search->created_time = $date_now;
                }

                $tmp_description = str_replace("&nbsp;"," ",$_item['description']);
                $model_index_search->title = $_item['title'];
                $model_index_search->description = $tmp_description;
                $model_index_search->book_id = $_item['book_id'];
                $model_index_search->post_id = $_item['post_id'];
                $model_index_search->search_key = $_item['search_key'];
                $model_index_search->image = $_item['image'];
                $model_index_search->updated_time = $date_now;
                $model_index_search->save(0);
            }
        }

    }

    public function actionBaidu(){
        $post_list = Posts::find()->where(['status' => 1])->orderBy("id asc")->all();
        if( !$post_list ){
            return $this->echoLog("no data for submit");
        }

        $urls = [];

        foreach( $post_list as $_post_info ){
            $urls[] = 'http://www.vincentguo.cn/default/'.$_post_info['id'];
        }

        $api = 'http://data.zz.baidu.com/urls?site=www.vincentguo.cn&token=Vtgp7UWu3EB7EQVt';
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        print_r($result);
    }

    public function actionRepair(){
        $post_list = Posts::find()->where(['status' => 1])->orderBy("id asc")->all();
        if($post_list){
            foreach( $post_list as $_post_info ){
                preg_match('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i',$_post_info['content'],$match_img);
                if( $match_img && count($match_img) == 3 ){
                    $_post_info->image_url = $match_img[2];
                }else{
                    $_post_info->image_url = "";
                }
                $_post_info->update(0);
            }
        }
    }
}