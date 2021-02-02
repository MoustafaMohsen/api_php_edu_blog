<?php
# please check thet guzzlehttp is compatible with the current PHP version https://github.com/guzzle/guzzle
require 'vendor/autoload.php';

//connection to wordpress api
$username = 'xxx';
$password = 'xxx';
$data=array(
    'imageUrl'=>'./test.jpg',
    'fileName'=>'Test Image',
    'originalFileName'=>'Test Image Original'
);

$client = new \GuzzleHttp\Client([
    'base_uri' => 'https://blog.educator.com/wp-json/wp/v2/',
    'headers' => [
        'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
        'Accept' => '*/*',
        // 'Content-type' => 'application/json',
        // 'Content-Disposition' => 'attachment',
    ]
]);

// uploading featured image to wordpress media and getting id
$imageOnMedia = $client->post(
    'media',
    [
        'multipart' => [
            [
                'name' => 'file',
                'contents' => file_get_contents($data['imageUrl']),
                'filename' => $data['fileName'],
            ],
    ],
    'query' => [
        'status' => 'publish',
        'title' => $data['originalFileName'],
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'alt_text' => $data['fileName'],
        'description' => '',
        'caption' => '',
    ],
]
);
$media = json_decode($imageOnMedia->getBody(), true);
$categoryId = $client->get('categories', ['query' => ['slug' => $data['deviceCategory']]]);
if(!empty($categoryId)) {
    $wpcategory = json_decode($categoryId->getBody());
} else {
    $wpcategory = 1;
}

$mediaStructure = '<!-- wp:gallery {"ids":['.$media['id'].'],"imageCrop":false,"linkTo":"attachment"} -->
<figure class="wp-block-gallery columns-1"><ul class="blocks-gallery-grid"><li class="blocks-gallery-item"><figure><a href="'.$media['link'].'"><img src="'.$media['source_url'].'" alt="werwerwer-1574671862.jpg" data-id="'.$media['id'].'" data-full-url="'.$media['source_url'].'" data-link="'.$media['link'].'" class="wp-image-'.$media['id'].'"/></a></figure></li></ul></figure>
<!-- /wp:gallery -->';

// uploading post to wordpress with featured image id
$post = $client->post(
    'posts', 
    [
    'multipart' => [
        [
            'name' => 'title',
            'contents' => $data['postTitle'],
        ],
        [
            'name' => 'content',
            'contents' => $mediaStructure
        ],
        [
            'name' => 'featured_media',
            'contents' => $media['id']
        ],
    ],
    'query' => [
        'status' => 'publish',
        'categories' => [$wpcategory[0]->id]
    ]
]);          
