<?php

namespace App\Services;

use App\Models\Wp_post_content;
use App\Models\Wp_post_info;
use GuzzleHttp\Client;
use  Exception;

class Wp_service{

    private $client;

    public function __construct()
    {
        $this->client=new Client();
    }

    public function postBlogContent($keyword,$title,$category,$content,$featured,$image,$domain,$login,$password,$post_date = null, $id = null){
        // dd($id);
        //$title = $title->input('title');
        //$content = $content->input('content');

        $errorList = [];
        $featuredImagePath = storage_path('app/public/'.$image);

        // Verifique se a imagem existe no caminho especificado
        if(!empty($image) && $featured==1) {
            // Obtenha o nome do arquivo da imagem
            $featuredImageName = basename($featuredImagePath);

            $additionalData = [
                'alt_text' => $keyword,
                'title' => $keyword,
                'caption' => $keyword,
                'description' => $keyword,
            ];

            // Faça a requisição para enviar a imagem para o WordPress
            try {
                $responseUploadImagem = $this->client->post($domain.'/wp-json/wp/v2/media', [
                    'auth' => [$login, $password],
                    'multipart' => [
                        [
                            'name' => 'file',
                            'contents' => fopen($featuredImagePath, 'r'),
                            'filename' => $featuredImageName,
                        ],

                        [
                            'name' => 'alt_text',
                            'contents' => $additionalData['alt_text'],
                        ],
                        [
                            'name' => 'title',
                            'contents' => $additionalData['title'],
                        ],
                        [
                            'name' => 'caption',
                            'contents' => $additionalData['caption'],
                        ],
                        [
                            'name' => 'description',
                            'contents' => $additionalData['description'],
                        ],
                    ],
                ]);
            } catch (Exception $e) {
                // Do nothing here, just continue execution
                $errorList[] = $e;
            }
            // Extraia o ID da imagem enviada
            $imageID = json_decode($responseUploadImagem->getBody())->id;
        } else {
            // Caso a imagem não seja encontrada, você pode tomar ação apropriada aqui
            $imageID = null;
}



            $category_slug = $category; // Substitua 'nome_da_categoria' pelo slug (nome) da categoria desejada

            $category_response = $this->client->get($domain.'/wp-json/wp/v2/categories?search=' . $category_slug);
            $category_data = json_decode($category_response->getBody(), true);
            if(!empty($category_data)){

                //$oldurl = $domain.'/wp-json/wp/v2/posts';
                $url = $domain."/wp-json/wp_manage/v1/post_create/";
                $data = [
                    'title' => $title,
                    'content' => $content,
                    'featured_media' => $imageID,
                    'status' => 'publish',
                    'categories'=>[$category_data[0]['id']],
                    'post_date' => date('Y-m-d H:i:s', strtotime($post_date))
                    // Adicione mais parâmetros conforme necessário
                ];
                $response = $this->client->post($url, [
                    'auth' => [$login, $password],
                    'form_params' => $data,
                ]);
                $post_response=json_decode($response->getBody(), true);
                $post_url=$post_response["post_url"];
                $post_id=$post_response["post_id"];
                $post_content = Wp_post_content::find($id);
                if($post_date > date('Y-m-d H:i:s')){
                    $posts_url_getter = $domain . '/wp-json/wp/v2/posts/' . $post_id;
                    $response_post = $this->client->post($posts_url_getter, [
                        'auth' => [$login, $password],
                        'form_params' => $data,
                    ]);
                    $post_data = json_decode($response_post->getBody(), true);;
                    $slug = $post_data['slug'];
                    $post_url = $domain . '/' . $slug;
                }


                if ($post_content) {
                    $post_content->update(['status' => 'publicado', 'post_url' => $post_url]);
                    $post_infos=Wp_post_info::create(['post_name'=>$post_content->theme,'post_url'=>$post_url,'post_id'=>$post_id,'Config_id'=>$post_content->id]);
                }
                // $change_status=Wp_post_content::where('theme',$title)->update(['status'=>'publicado']);
                // $insert_url=Wp_post_content::where('theme',$title)->update(['post_url'=>$post_url]);

                return $response->getBody();

            }else{
                return 'nome da categoria não encontrado';
            }





    }
    public function updateYoastRankScore($domain,$post_id,$keyword){

        $data = array(
            'post_id' => $post_id,
            'keyword' => $keyword
        );


        $ch = curl_init($domain.'/wp-json/wp_manage/v1/update_yoast_score/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        curl_close($ch);


        // Verifique a resposta
        if ($response === false) {
            echo 'Erro ao fazer a requisição cURL: ' . curl_error($ch);
        } else {
            return $response;
        }
    }
    public function updateYoastRankMath($domain,$post_id,$keyword){
        //dd($domain,$keyword,$post_id);
        $data = array(
            'post_id' => $post_id,
            'keyword' => $keyword
        );

        $ch = curl_init($domain.'/wp-json/wp_manage/v1/update_yoast_keyword/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
        // Verifique a resposta
        if ($response === false) {
            echo 'Erro ao fazer a requisição cURL: ' . curl_error($ch);
        } else {
            echo 'Resposta da requisição: ' . $response;
        }
    }

    public function deletePost($id,$domain,$login,$password){
        $deletePostFromPlataform=$this->client->delete($domain.'/wp-json/wp/v2/posts/'.$id,['auth'=>[$login,$password]]);
        return $deletePostFromPlataform;
    }
}
