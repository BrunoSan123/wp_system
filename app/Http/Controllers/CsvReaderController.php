<?php

namespace App\Http\Controllers;

use App\Services\CsvReaderService;
use App\Services\PostContentService;
use App\Services\PostFileService;
use Illuminate\Http\Request;
use DateTime;

class CsvReaderController extends Controller
{
    protected $reader;
    protected $postConfigService;
    protected $imageService;

    public function __construct(CsvReaderService $readerService,PostFileService $postConfig,PostContentService $imageService)
    {
        $this->reader=$readerService;
        $this->postConfigService=$postConfig;
        $this->imageService=$imageService;
    }



    public function ImportCsv(Request $request){
        $valorCodificado = request()->cookie('editor');
        $user=explode('+',base64_decode($valorCodificado));
        if($request->hasFile('csv_file')){
            $data_csv=$this->reader->CsvToJson($request);
            foreach ($data_csv as $key => $row) {
                foreach ($row as $subKey => $value) {
                    $data_csv[$key][$subKey] = is_string($value) ? utf8_encode($value) : $value;
                }
            }

            $data=$data_csv;
            $newData=array_pop($data);
            $c=[];
            foreach($data as $dt){
                $dataAtual = new DateTime();
                if(intval($dt['Programacao de Postagem'])<0){
                   $dataAtual->modify('-' . intval($dt['Programacao de Postagem']) . ' days');
                }else{
                    $dataAtual->modify('+' . intval($dt['Programacao de Postagem']) . ' days');
                }
                
                $addImage=null;
                $dataAtual->format('Y-m-d H:i:s');
                $video = trim($dt['Video']," \t\n\r\0\x0B");

                 $url = $dt['Imagem'];
                 $path = parse_url($url, PHP_URL_PATH);

                 $folders_part = explode('/folders/', $path)[1];
                 if(!$folders_part){
                    $addImage=$this->imageService->downloadImageFromUrl($url);
                 }
                 $folders_part_without_query = strstr($folders_part, '?', true);
                 $dataUser=array('session_user'=>$user[0],'gdrive_url'=>$folders_part);
                 $teste=json_encode($dataUser);
                 $userData=json_decode($teste);
                 if(isset($dt['Imagem'])){
                     $addImage=$this->imageService->downloadImageFromGoogleDrive('',$userData);
                 }

                $content=array(
                    'theme'=>$dt['Tema'],
                    'keyword'=>$dt['Keyword'],
                    'category'=>$dt['Categoria'],
                    'anchor_1'=>$dt['Ancora 1'],
                    'url_link_1'=>$dt['URL do Link 1'],
                    'url_link_3'=>$dt['URL do Link 3'],
                    'do_follow_link_1' => isset($dt['Dofollow_link_1']) && $dt['Dofollow_link_1'] === 'Sim' ? true : null,
                    'anchor_2'=>$dt['Ancora 2'],
                    'url_link_2'=>$dt['URL do Link 2'],
                    'do_follow_link_2' => isset($dt['Dofollow_link_2']) && $dt['Dofollow_link_2'] === 'Sim' ? true : null,
                    'anchor_3'=>$dt['Ancora 3'],
                    'do_follow_link_3' => isset($dt['Dofollow_link_3']) && $dt['Dofollow_link_3'] === 'Sim' ? true : null,
                    'internal_link'=>isset($dt['Link Interno']) && $dt['Link Interno']==='Sim'?true:null,
                    'domain'=>$dt['Dominio'],
                    'gdrive_document_url'=>$dt['Gdrive'],
                    'video'=>isset($video)&& $video=== 'Sim'? true:null,
                    'schedule_date'=>$dataAtual,
                    'insert_image'=>isset($dt['Insere Imagem no Post']) && $dt['Insere Imagem no Post']==='Sim'?true:null,
                    'post_image'=>$addImage,
                    'user_id'=>$request->user_id,

                );

                $new_csv_content=$this->postConfigService->insertCSV($content);

            }

            //dd($processed_data)
        }

        return redirect()->route('configCreated');
    }
}
