<?php

namespace App\Http\Controllers;

use App\Models\Wp_credential;
use App\Models\Wp_post_content;
use App\Models\Wp_post_info;
use Illuminate\Http\Request;
use App\Services\Wp_service;
use Illuminate\Support\Facades\Storage;

class ConfigDeleteController extends Controller
{
    protected $wpService;

    public function __construct(Wp_service $service) {
        $this->wpService=$service;
    }
    //
    public function deleteConfig(Request $request){
        $delete_request =Wp_post_content::find($request->id);
        $post_credentials=Wp_credential::where('wp_domain',$delete_request->domain)->get()[0];
        $delete_post=Wp_post_info::where('Config_id',$request->id)->get()[0];
        if(!empty($post_credentials)){
            $deleteFromPlataform=$this->wpService->deletePost($delete_post->post_id,$post_credentials->wp_domain,$post_credentials->wp_login,$post_credentials->wp_password);
        }
        if(!empty($delete_post)){
            $delete_post->delete();
        }
        if ($delete_request->post_image!=null  && Storage::disk('public')->exists($delete_request->post_image)) {
            // Deleta o arquivo
            Storage::disk('public')->delete($delete_request->post_image);
             // Retorna verdadeiro se a exclusão for bem-sucedida
        }
        $deletion=$delete_request->delete();
        return response()->json($deletion, 200);
    }
}
