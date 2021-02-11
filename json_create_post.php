<?php
require 'vendor/autoload.php';

class JsonCreatePost {

    public $username;
    public $password;
    public $data;
    function __construct($user,$pass,$post_data) {
         $this->username = $user;
         $this->password = $pass;
         $this->data=$post_data;
    }
    //connection to wordpress api

    public function create()
    {        
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://blog.educator.com/wp-json/wp/v2/',
            'headers' => [
                'A-Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
                'Accept' => '*/*',
                'Content-type' => 'application/json',
                'Content-Disposition' => 'attachment',
            ]
        ]);
    
        // uploading featured image to wordpress media and getting id
        try {
            // uploading post to wordpress with featured image id
            $post = $client->post(
                'posts', 
                [
                'multipart' => [
                    [
                        'name' => 'title',
                        'contents' => $this->data['postTitle'],
                    ],
                    [
                        'name' => 'content',
                        'contents' => $this->data['content']
                    ],
                ],
                'query' => [
                    'status' => 'publish',
                    'categories' => $this->data['category_id']
                ]
            ]);
            http_response_code($post->getStatusCode());
            return json_encode($post);
        } catch(\GuzzleHttp\Exception\RequestException $e){
            $error['error'] = $e->getMessage();
            $error['request'] = $e->getRequest();
    
            if($e->hasResponse()){
                if ($e->getResponse()->getStatusCode() == '400'){
                    $error['response'] = $e->getResponse(); 
                }
            }
            http_response_code($e->getResponse()->getStatusCode());
            return json_encode($error);
        }catch(Exception $e){
            throw $e;
        }
    }
}
