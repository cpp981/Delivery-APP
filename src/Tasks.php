<?php
session_start();
require '../vendor/autoload.php';
//$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$redirect_uri = 'http://127.0.0.1' . $_SERVER['PHP_SELF'];
$client = new Google_Client();
$client->setApplicationName('repartos');
//$client->setApplicationName('Google Tasks API PHP');
$client->setAuthConfig(__DIR__.'/credentials.json');
$client->setRedirectUri($redirect_uri);
$client->setScopes(Google_Service_Tasks::TASKS); //TASKS_READONLY
$client->setPrompt('select_account consent');

// Por si queremos cerrar sesion 
if (isset($_REQUEST['logout'])) {
    unset($_SESSION['token']);
}

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    // Guardamos el token en una variable de sesion
    $_SESSION['token'] = $token;

    // redirigimas a esta misma página
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

// set the access token as part of the client
if (!empty($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
    if ($client->isAccessTokenExpired()) {
        unset($_SESSION['token']);
    }
} else {
    $authUrl = $client->createAuthUrl();
    header("Location:$authUrl");
}
