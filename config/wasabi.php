<?php

return [
    'access_key' => env('WAS_ACCESS_KEY'),
    'secret_key' => env('WAS_SECRET_KEY'),
    'bucket' => env('WASABI_BUCKET'),
    'endpoint' => env('WASABI_ENDPOINT'),
    'region' => env('WASABI_REGION'),
    'url' => env('WASABI_URL'),
    'avatar_directory' => 'images/avatar',
    'etablissement_logo_directory' => 'etablissement/logo',
    'etablissement_cover_directory' => 'etablissement/cover',
    'article_image_directory' => 'articles/image',
    'promotion_image_directory' => 'promotions/image',
    'annonce_image_directory' => 'images/annonce',
];
