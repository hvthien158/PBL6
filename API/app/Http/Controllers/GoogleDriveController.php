<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\GoogleToken;
use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
class GoogleDriveController extends Controller
{
    public $gClient;

    function __construct()
    {

        $this->gClient = new Client();

        $this->gClient->setApplicationName(env('APP_NAME'));
        $this->gClient->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
        $this->gClient->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
        // $this->gClient->setRedirectUri(route('google.login'));
        $this->gClient->setDeveloperKey(env('API_KEY'));
        $this->gClient->setScopes(
            array(
                'https://www.googleapis.com/auth/drive.file',
                'https://www.googleapis.com/auth/drive'
            )
        );

        $this->gClient->setAccessType("offline");

        $this->gClient->setApprovalPrompt("force");
    }
    public function googleDriveFileUpload($image)
    {
        if ($image) {
            $file = $image;
            $fileData = file_get_contents($file->getRealPath());
            $base64Data = base64_encode($fileData);
            $service = new Drive($this->gClient);
            $tokenGoogle = GoogleToken::find(1);

            $this->gClient->setAccessToken(json_decode($tokenGoogle->access_token, true));

            if ($this->gClient->isAccessTokenExpired()) {
                $refreshTokenSaved = $tokenGoogle->refresh_token;
                $this->gClient->fetchAccessTokenWithRefreshToken($refreshTokenSaved);
                $updatedAccessToken = $this->gClient->getAccessToken();
                $updatedAccessToken['refresh_token'] = $refreshTokenSaved;
                $this->gClient->setAccessToken($updatedAccessToken);
                $tokenGoogle->access_token = $updatedAccessToken;
                $tokenGoogle->refresh_token = $this->gClient->getRefreshToken();
                $tokenGoogle->save();
            }
            $folderName = 'Avatar';
            $folderList = $service->files->listFiles(array('q' => "mimeType='application/vnd.google-apps.folder' and name='$folderName'"));

            if (count($folderList->getFiles()) > 0) {
                $folder = $folderList->getFiles()[0];
            } else {
                $fileMetadata = new DriveFile(
                    array(
                        'name' => $folderName,
                        'mimeType' => 'application/vnd.google-apps.folder'
                    )
                );
                $folder = $service->files->create($fileMetadata, array('fields' => 'id'));
            }
            $file = new DriveFile(array('name' => Str::random(10) . '.jpg', 'parents' => array($folder->getId())));
            $result = $service->files->create(
                $file,
                array(
                    'data' => base64_decode($base64Data),
                    'mimeType' => 'application/octet-stream',
                    'uploadType' => 'media'
                )
            );
            $fileId = $result->id;
            $file = $service->files->get($fileId, array('fields' => 'thumbnailLink'));
            $fileUrl = $file->thumbnailLink;
            return $fileUrl;
        }
        return false;
    }
}