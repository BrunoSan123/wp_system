<?php

namespace App\Http\Controllers;

use App\Models\Editor;
use App\Models\Ia_credential;
use Illuminate\Http\Request;

class DasboardController extends Controller
{
    //

    public function index()
    {
        return view('dashboard.index');
    }

    public function login(){
        return view('login');
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

    public function register(){
        return view('dashboard.register');
    }

    public function ediorCreated(){
        return view('dashboard.editorCreated');
    }

    public function configCreated(){
        return view('dashboard.configCreated');
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

    public function listPostConfig(){
        return view('dashboard.SubmitPosts');
    }  
    
    public function docCreated(){
        return view('dashboard.DocumentCreated');
    }

    public function docImported(){
        return view('dashboard.DocumentImported');
    }

    public function tokenInserted(){
        return view('dashboard.tokeninserted');
    }

    public function importCsv(){
        return view('dashboard.upload');
    }

    public function insertGptToken(){
        if(empty(Ia_credential::all())){
            $token=null;
        }else{
            $token=Ia_credential::all();
        }

        $valorCodificado = request()->cookie('editor');
        $user=explode('+',base64_decode($valorCodificado));
        
        return view('dashboard.configIa',['ia_token'=>$token,'editor'=>$user[0]]);
    }

    public function insertWpCredential(){
        return view('dashboard.wordpressCredential');
    }

    public function listWpCredential(){
        $user_credentials=Editor::all();
        $editor_credentials=[];
        foreach($user_credentials as $credentials){
            if(!empty($credentials->links)){
                foreach ($credentials->links as $link) {
                    $editor_credentials[] = $link;
                }
            }
            
        }
        return view('dashboard.wpCredentialList',['credentiais'=>$editor_credentials,'editor'=>$user_credentials]);

    }

    public function siteCredentialCreated(){
        return view('dashboard.SiteCredentialCreated');
    }

}
