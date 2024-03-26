<?php

namespace App\Http\Controllers;

use App\Models\Wp_post_content;
use App\Services\CsvReaderService;
use App\Services\PostContentService;
use App\Services\PostFileService;
use Illuminate\Http\Request;

class GooGleDocsController extends Controller
{
    //

    protected $googleDocsService;
    protected $postService;

    public function __construct(PostFileService $googleDocs)
    {
        $this->googleDocsService=$googleDocs;
    }

    public function insertDocOnDB(Request $request){
        if (!$request->session()->has('google_access_token')) {
            // Se o token de acesso não estiver presente, redirecione para o processo de autenticação do Google
            return redirect()->route('google.redirect');
        }
        if($request->has('google_docs')){
            $doc_content=$this->googleDocsService->importGoogleDocs($request->google_docs);
            $content=Wp_post_content::where('theme',$request->title)->update(['post_content'=>$doc_content]);
            return $content;
        }
    }

    public function createDocFromDb(Request $request){

        if (!$request->session()->has('google_access_token')) {
            // Se o token de acesso não estiver presente, redirecione para o processo de autenticação do Google
            return redirect()->route('google.redirect');
        }
        //title não é unique key
        // $content=Wp_post_content::where('theme',$request->theme)->get();
        $content=Wp_post_content::find($request->id);
        // dd($content);
        if(empty($content->post_content)){
            $content->post_content = ' ';
        }
        $doc_created=$this->googleDocsService->createAndPopulateGoogleDoc($content->theme,$content->post_content,$request->folder_id);

        return $doc_created;
    }
}
