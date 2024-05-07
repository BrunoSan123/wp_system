<?php

namespace App\Services;
use App\Models\Wp_post_content;
use App\Models\Wp_credential;
use Google\Client;
use Google\Service\Docs;
use Google\Service\Drive;
use Google\Service\Docs\Request;
use Google\Service\Docs\TextStyle; //isso pode add style para o doc google


class PostFileService{


    public function showUploadForm()
    {
        return view('GoogleDocs');
    }

    public function insertCSV($data){
        $new_content=Wp_post_content::create([
            'theme'=>isset($data['theme'])?$data['theme']:null,
            'keyword'=>isset($data['keyword'])?$data['keyword']:null,
            'category'=>isset($data['category'])?$data['category']:null,
            'anchor_1'=>isset($data['anchor_1'])?$data['anchor_1']:null,
            'url_link_1'=>isset($data['url_link_1'])?$data['url_link_1']:null,
            'anchor_2'=>isset($data['anchor_2'])?$data['anchor_2']:null,
            'url_link_2'=>isset($data['url_link_2'])?$data['url_link_2']:null,
            'do_follow_link_1'=>isset($data['do_follow_link_1']) && $data['do_follow_link_1'] === true ? 1 : 0,
            //'anchor_2'=>isset($data['anchor_2'])?$data['anchor_2']:null,
            'do_follow_link_2'=>isset($data['do_follow_link_2']) && $data['do_follow_link_2'] === true ? 1 : 0,
            'anchor_3'=>isset($data['anchor_3'])?$data['anchor_3']:null,
            'url_link_3'=>isset($data['url_link_3'])?$data['url_link_3']:null,
            'do_follow_link_3'=>isset($data['do_follow_link_3']) && $data['do_follow_link_3'] === true ? 1 : 0,
            'internal_link'=>isset($data['internal_link'])?$data['internal_link']:null,
            'post_content'=>isset($data['post_content'])?$data['post_content']:null,
            'insert_image'=>isset($data['insert_image']) && ($data['insert_image'] === 'Sim') ? 1 : 0,
            'domain'=>isset($data['domain'])?$data['domain']:null,
            'gdrive_document_url'=>isset($data['gdrive_document_url'])?$data['gdrive_document_url']:null,
            'video'=>isset($data['video']) && $data['video']===true?1:0,
            'schedule_date'=>isset($data['schedule_date'])?$data['schedule_date']:null,
            'insert_image'=>isset($data['insert_image']) && ($data['insert_image']===true)?1:0,
            'post_image'=>isset($data['post_image'])?$data['post_image']:null,
            'status'=>'Não publicado',
            'Editor_id'=>$data['user_id']
        ]);
    }

    public function insertSiteCsv($data){
        $new_site=Wp_credential::create([
            'wp_login'=>isset($data['wp_login'])?$data['wp_login']:null,
            'wp_password'=>isset($data['wp_password'])?$data['wp_password']:null,
            'wp_domain'=>isset($data['wp_domain'])?$data['wp_domain']:null,
            'Editor_id'=>$data['user_id']
        ]);
        return $new_site;
    }

    public function importGoogleDocs($id)
    {
        // Ler o token de acesso da sessão
        $accessToken = session('google_access_token');
        // Se o token de acesso não estiver presente na sessão, redirecione para o processo de autenticação do Google
        if (!$accessToken) {
            return redirect()->route('google.redirect');
        }

        // Criar cliente Google
        $client = new Client();
        $client->setAccessToken($accessToken);

        // Inicializar serviço de Documentos do Google
        $service = new Docs($client);

        try {
            // Fazer a solicitação para obter o conteúdo do documento
            $response = $service->documents->get($id);
            $content=$response->getBody()->getContent();
            $post_content='';

            foreach ($content as $element) {
                if (isset($element->paragraph)) {
                    $paragraph = $element->paragraph;
                    foreach ($paragraph->elements as $paragraphElement) {
                        if (isset($paragraphElement->textRun)) {
                            $textRun = $paragraphElement->textRun;
                             $post_content.=$textRun->content;
                        }
                    }
                }
            }
            return $post_content;


        } catch (\Exception $e) {
            // Lidar com qualquer erro que ocorra ao tentar obter o conteúdo do documento
            return redirect()->back()->with('error', 'Failed to fetch document content: ' . $e->getMessage());
        }
    }

    public function createAndPopulateGoogleDoc($title,$data,$folderId)
    {
        $accessToken = session('google_access_token');

        if (!$accessToken) {
            return redirect()->route('google.redirect');
        }

        $client = new Client();
        $client->setAccessToken($accessToken);

       $driveService = new Drive($client);
       $service = new Docs($client);

        try {
            // $document = new \Google\Service\Docs\Document([
            //     'title' => $title
            // ]);

            $driveDocument = new \Google\Service\Drive\DriveFile([
                'name' => $title,
                'parents' => [$folderId], // Especifica a pasta pai
                'mimeType' => 'application/vnd.google-apps.document'
            ]);

            $createdDocument = $driveService->files->create($driveDocument, array(
                'fields' => 'id'
            ));

           // $createdDocument = $service->documents->create($document);
            $documentId = $createdDocument->id;
            // Adiciona conteúdo ao documento
            $requests = [
                new \Google\Service\Docs\Request([
                    'insertText' => [
                        'location' => [
                            'index' => 1,
                        ],
                        'text' => $data
                    ]
                ])
            ];

            $batchUpdateRequest = new \Google\Service\Docs\BatchUpdateDocumentRequest([
                'requests' => $requests
            ]);

            $service->documents->batchUpdate($documentId, $batchUpdateRequest);

            return response()->json('Documento criado e populado com sucesso! Link: ' . "https://docs.google.com/document/d/$documentId/edit", 200);
        } catch (\Exception $e) {
            //return redirect()->back()->with('error', 'Falha ao criar o documento: ' . $e->getMessage());
            return "$e";
        }
    }



}
