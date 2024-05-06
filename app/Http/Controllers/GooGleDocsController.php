<?php

namespace App\Http\Controllers;

use App\Models\Wp_post_content;
use App\Services\CsvReaderService;
use App\Services\PostContentService;
use App\Services\PostFileService;
use Illuminate\Http\Request;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Str;

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

        $googleDocsContent = "## hello world";//$this->convertToGoogleDocsFormat($content->post_content);

        $doc_created=$this->googleDocsService->createAndPopulateGoogleDoc($content->theme,$googleDocsContent,$request->folder_id);

        return $doc_created;
    }

    public function convertToGoogleDocsFormat($htmlContent) {
        return $htmlContent;
        // Load HTML content into DOMDocument
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Suppress HTML5 parsing errors
        $dom->loadHTML($htmlContent);
        libxml_clear_errors();

        // XPath query to select all elements
        $xpath = new DOMXPath($dom);
        $elements = $xpath->query('//*');

        // Loop through each element and convert to Google Docs compatible format
        foreach ($elements as $element) {
            // Convert heading tags
            if ($element->tagName == 'h1') {
                $element->nodeValue = "### " . $element->nodeValue;
            } elseif ($element->tagName == 'h2') {
                $element->nodeValue = "## " . $element->nodeValue;
            } elseif ($element->tagName == 'h3') {
                $element->nodeValue = "# " . $element->nodeValue;
            }

            // Convert bold and italic tags
            if ($element->tagName == 'strong') {
                $element->nodeValue = "**" . $element->nodeValue . "**";
            } elseif ($element->tagName == 'em') {
                $element->nodeValue = "*" . $element->nodeValue . "*";
            }

            // You may add more conversions for other HTML tags as needed
        }

        // Get the modified HTML content
        $modifiedHtml = $dom->saveHTML();

        // Remove the default doctype, <html>, and <body> tags added by saveHTML()
        $modifiedHtml = Str::replaceFirst('<!DOCTYPE html>', '', $modifiedHtml);
        $modifiedHtml = Str::after($modifiedHtml, '<body>');
        $modifiedHtml = Str::beforeLast($modifiedHtml, '</body>');

        return $modifiedHtml;
    }
}
