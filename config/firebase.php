<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Web & Messaging Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase client SDK and server-side notifications.
    |
    */
    'api_key' => env('FIREBASE_API_KEY', 'AIzaSyBdoAaFK4obS82aO9D-oqWn5dYgYLFJKRM'),
    'auth_domain' => env('FIREBASE_AUTH_DOMAIN', 'mdtnotification.firebaseapp.com'),
    'project_id' => env('FIREBASE_PROJECT_ID', 'mdtnotification'),
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', 'mdtnotification.firebasestorage.app'),
    'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID', '330219223368'),
    'app_id' => env('FIREBASE_APP_ID', '1:330219223368:web:1a318d1fb0ae5ac1511b7d'),
    'measurement_id' => env('FIREBASE_MEASUREMENT_ID', 'G-0YDHT2GN43'),
    'vapid_key' => env('FIREBASE_VAPID_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | Firebase Admin / Server-side Credentials
    |--------------------------------------------------------------------------
    |
    | For Firebase Cloud Messaging HTTP v1 API, provide path to service account JSON,
    | or raw JSON string, or legacy server key.
    |
    */
    'credentials_path' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase/service-account.json')),
    'credentials_json' => env('FIREBASE_CREDENTIALS_JSON', null),
    'server_key' => env('FIREBASE_SERVER_KEY', null),
];
