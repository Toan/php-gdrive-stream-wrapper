<?php
include_once __DIR__ . '/vendor/autoload.php';
require_once './GoogleDriveStreamWrapper.php';

session_start();
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$client = new Google_Client();
$client->setAuthConfigFile('client_secrets.json');
$client->addScope(Google_Service_Drive::DRIVE);
$client->setRedirectUri($redirect_uri);
$client->setAccessType('offline');

if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
    if ($client->isAccessTokenExpired()) {
        $tokens_decoded = json_decode($_SESSION['access_token']);
        $refreshToken = $tokens_decoded->refresh_token;
        $client->refreshToken($refreshToken);
        $_SESSION['access_token'] = $client->getAccessToken();
    }

    $service = new Google_Service_Drive($client);
    \GoogleDriveStreamWrapper::setSrvice($service);
    \GoogleDriveStreamWrapper::registerWrapper();
} else {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
}

var_dump(mkdir('gdrive://aaa'));
var_dump(mkdir('gdrive://aaa/bbb2'));
var_dump(rename('gdrive://aaa/bbb2', 'gdrive://aaa/bbb3'));
var_dump(rmdir('gdrive://aaa/bbb3'));
var_dump(mkdir('gdrive://aaa/bbb4'), is_dir('gdrive://aaa/bbb4'), is_file('gdrive://aaa/bbb4'));

$path = 'gdrive://aaa/bbb4/' . date('Y-m-d-H-i-s') . '.txt';
var_dump(file_put_contents($path, 'test'));
var_dump(file_get_contents($path));
var_dump(file_put_contents($path, ' test2', FILE_APPEND));
var_dump(file_get_contents($path));
var_dump(filesize($path), is_dir($path), is_file($path));
