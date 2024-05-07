<?php

namespace App\Http\Controllers;

use App\Models\Editor;
use App\Models\Ia_credential;
use App\Models\Wp_credential;
use Illuminate\Http\Request;
use App\Models\Wp_post_content;


class DasboardController extends Controller
{
    //

    public function index()
    {
        return view('dashboard.index');
    }

    public function login(){
        $editors = Editor::all();
        if(!empty($editors[0]->name)){
            return view('login');
        }else{
            return view('userBootstrap');
        }

    }

    public function show(Request $request)
    {
        $page = $request->input('page', 'home');
        return view('dashboard.show', compact('page'));
    }

    public function profile()
    {
        return view('dashboard.profile');
    }

    public function ListEditor(){
        $valorCodificado = request()->cookie('editor');
        $user=explode('+',base64_decode($valorCodificado));
        $user_session=Editor::where('name',$user[0])->get();
        $editors = null;
        if($user_session[0]->is_admin!=1){
            $editors = $user_session;
        }else{
            $editors = Editor::all();
        }
        return view('dashboard.editorList',['editors'=>$editors]);
    }

    public function register(){
        return view('dashboard.register');
    }

    public function ediorCreated(){
        return view('dashboard.editorCreated');
    }

    public function configCreated(){
        return view('dashboard.configCreated');
    }

    public function configUpdated(){
        return view('dashboard.configUpdate');
    }

    public function contentCreation(){
        return view('dashboard.contentConfig');
    }

    public function postCreation(){
        return view('dashboard.createPost');
    }

    public function DocsUpload(){
        return view('GoogleDocs');
    }

    public function DocCreation(){
        return view('dashboard.GoogleDocCreation');
    }

    public function listPostConfig(Request $request){
        $results=null;
        if(!empty($request->input('query'))){
            $results=Wp_post_content::where ('theme','like','%'.$request->input('query').'%')->orWhere('domain','like','%'.$request->input('query').'%')->get();
        }

        return view('dashboard.SubmitPosts',['search'=>$results]);
    }

    public function docCreated(){
        return view('dashboard.DocumentCreated');
    }

    public function docImported(){
        return view('dashboard.DocumentImported');
    }

    public function tokenInserted(){
        return view('dashboard.tokenInserted');
    }

    public function importCsv(){
        return view('dashboard.upload');
    }
    public function gDrivePort(){
        return view('dashboard.gdriveConfig');
    }
    public function yoastforce(){
        return view('dashboard.yoastforce');
    }

    public function gptTeste(){

        $valorCodificado = request()->cookie('editor');
        $user=explode('+',base64_decode($valorCodificado));
        $editor=Editor::where('name',$user)->get();
        $contents=Editor::find($editor[0]->id);

        return view('demandTest',['contents'=>$contents->postContents]);
    }


    public function insertGptToken(){
        $valorCodificado = request()->cookie('editor');
        $user=explode('+',base64_decode($valorCodificado));
        if(empty(Editor::where('name',$user[0])->get())){
            $token=null;
        }else{
            $token_query=Editor::where('name',$user[0])->get();
            $token=Editor::find($token_query[0]->id)->iaCredentials;
        }


        return view('dashboard.configIa',['ia_token'=>$token,'editor'=>$user[0]]);
    }

    public function insertWpCredential(){
        return view('dashboard.wordpressCredential');
    }

    public function listWpCredential(Request $request){
        $valorCodificado = request()->cookie('editor');
        $user=explode('+',base64_decode($valorCodificado));
        $user_session=Editor::where('name',$user[0])->get();
        $user_credentials =$user_session;
        $search_user=[];
        $results=[];
        // if($user_session[0]->is_admin!=1){
        //     $user_credentials = $user_session;
        // }else{
        //     $user_credentials = Editor::all();
        // }
        //$user_credentials=Editor::all();
        $editor_credentials=[];
        foreach($user_credentials as $credentials){
            if(!empty($credentials->links)){
                foreach ($credentials->links as $link) {
                    $editor_credentials[] = $link;
                }
            }
        }


        if(!empty($request->input('query'))){
            $search_user=$user_session[0];
            $results=$search_user->links()->where ('wp_login','like','%'.$request->input('query').'%')->orWhere('wp_domain','like','%'.$request->input('query').'%')->get();
        }
        return view('dashboard.wpCredentialList',['credentiais'=>$editor_credentials,'editor'=>$user_credentials,'searchUser'=>$search_user,'search'=>$results,'user_page'=>$user[0]]);

    }


    public function listIaCredentials(){
        $user_credentials=Editor::all();
        $editor_ia_credentials=[];
        foreach($user_credentials as $credentials){
            if(!empty($credentials->iaCredentials)){
                foreach ($credentials->iaCredentials as $iaCredential) {
                    $editor_ia_credentials[] = $iaCredential;
                }
            }

        }

        return view('dashboard.IatokenList',['Iacredentials'=>$editor_ia_credentials,'editor'=>$user_credentials]);

    }

    public function siteCredentialCreated(){
        return view('dashboard.SiteCredentialCreated');
    }

    public function tokenDeleted(){
        return view('dashboard.deletedToken');
    }

    public function registerSiteTeste(){
        return view('dashboard.siteUpload');
    }

    public function changelog(){
        $changelog = file_get_contents(public_path('log.csv'));
        $changelog = explode("\n", $changelog);
        return view('dashboard.changelog', ['changelog' => $changelog]);
    }


}
