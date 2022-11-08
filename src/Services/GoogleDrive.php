<?php

declare(strict_types=1);

namespace App\Services;

use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Service\Drive\Permission;
use Google\Service\Exception;
use GuzzleHttp\Psr7\Response;

class GoogleDrive
{

    /** @var Drive */
    private $service;

    public function __construct(GoogleDriveClient $client)
    {
        $this->service = new Drive($client->getClient());
    }

    public function uploadBasic()
    {
        $driveService = $this->service;
        // [START drive_upload_basic]
        $fileMetadata = new DriveFile(array(
            'name' => 'test.jpg'
        ));
        $content = file_get_contents($_ENV['SO_DATA_PATH'] . 'test.jpg');
        $file = $driveService->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => 'image/jpeg',
            'uploadType' => 'multipart',
            'fields' => 'id'
        ));
        printf("File ID: %s\n", $file->id);
        // [END drive_upload_basic]
        return $file->id;
    }

    public function uploadToFolder($realFolderId, $fileName, $path)
    {
        $driveService = $this->service;
        $folderId = $realFolderId;
        
        $fileMetadata = new DriveFile(array(
            'name' => $fileName,
            'parents' => array($folderId)
        ));
        
        $filePath = $path . $fileName;
        $mime = mime_content_type($filePath);
        $content = file_get_contents($filePath);
        
        $file = $driveService->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => $mime,
            'uploadType' => 'multipart',
            'fields' => 'id'
        ));
        
        return $file->id;
    }

    public function uploadFile(string $fileName, string $path, ?string $folderId = null)
    {
        $filePath = $path . $fileName;
        $mime = mime_content_type($filePath);
        $content = file_get_contents($filePath);

        return $this->uploadFileContent($fileName, $mime, $content, $folderId);
    }

    public function uploadFile2(string $fileName, string $path, ?string $folderId = null)
    {
        $mime = mime_content_type($path);
        $content = file_get_contents($path);

        return $this->uploadFileContent($fileName, $mime, $content, $folderId);
    }

    public function uploadFileContent(
        string $fileName, string $mime, 
        string $content, ?string $folderId = null
        ): DriveFile
    {
        $driveService = $this->service;
        $parameters = ['name' => $fileName];

        if ($folderId) {
            $parameters['parents'] = [$folderId];
        }

        $fileMetadata = new DriveFile($parameters);

        $file = $driveService->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => $mime,
            'uploadType' => 'multipart',
            'fields' => 'id'
        ));
        
        return $file;
    }

    public function uploadWithConversion()
    {
        $driveService = $this->service;
        // [START drive_upload_with_conversion]
        $fileMetadata = new DriveFile(array(
            'name' => 'My Report',
            'mimeType' => 'application/vnd.google-apps.spreadsheet'
        ));
        $content = file_get_contents('files/report.csv');
        $file = $driveService->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => 'text/csv',
            'uploadType' => 'multipart',
            'fields' => 'id'
        ));
        
        printf("File ID: %s\n", $file->id);
        // [END drive_upload_with_conversion]
        return $file->id;
    }

    // TODO - Currently broken due to PHP client confiscating 'mimeType' param
    public function exportPdf($realFileId)
    {
        $driveService = $this->service;
        // [START drive_export_pdf]
        $fileId = '1ZdR3L3qP4Bkq8noWLJHSr_iBau0DNT4Kli4SxNc2YEo';
        // [START_EXCLUDE silent]
        $fileId = $realFileId;
        // [END_EXCLUDE]
        $response = $driveService->files->export($fileId, 'application/pdf', array(
            'alt' => 'media'
        ));
        $content = $response->getBody()->getContents();
        // [END drive_export_pdf]
        return $content;
    }

    public function downloadFile($realFileId): Response
    {
        $driveService = $this->service;
        
        /** @var Response */
        $response = $driveService->files->get($realFileId, array(
            'alt' => 'media'
        ));

        return $response;
    }

    public function createShortcut()
    {
        $driveService = $this->service;
        // [START drive_create_shortcut]
        $fileMetadata = new DriveFile(array(
            'name' => 'Project plan',
            'mimeType' => 'application/vnd.google-apps.drive-sdk'
        ));
        $file = $driveService->files->create($fileMetadata, array(
            'fields' => 'id'
        ));
        printf("File ID: %s\n", $file->id);
        // [END drive_create_shortcut]
        return $file->id;
    }

    public function touchFile($realFileId, $realModifiedTime)
    {
        $driveService = $this->service;
        // [START drive_touch_file]
        $fileId = '1sTWaJ_j7PkjzaBWtNc3IzovK5hQf21FbOw9yLeeLPNQ';
        $fileMetadata = new DriveFile(array(
            'modifiedTime' => date('Y-m-d\TH:i:s.uP')
        ));
        // [START_EXCLUDE silent]
        $fileId = $realFileId;
        $fileMetadata->modifiedTime = $realModifiedTime;
        // [END_EXCLUDE]
        $file = $driveService->files->update($fileId, $fileMetadata, array(
            'fields' => 'id, modifiedTime'
        ));
        printf("Modified time: %s\n", $file->modifiedTime);
        // [END drive_touch_file]
        return $file->modifiedTime;
    }

    public function createFolderHierarchy(string $value)
    {
        $names = explode('/', $value);
        $lastId = null;
        $folders = [];

        foreach ($names as $name) {

            if (empty(trim($name))) {
                continue;
            }

            $file = $this->createFolder($name, $lastId);            
            $lastId = $file->id;
            $folders[$name] = $file;
        }

        return $folders;
    }

    public function createFolder(?string $name, ?string $parentId = null)
    {
        if (!$name) {
            $name = 'folder_' . time();
        }

        $driveService = $this->service;

        $metadata = array(
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder'
        );

        if ($parentId) {
            $metadata['parents'] = array($parentId);
        }

        $fileMetadata = new DriveFile($metadata);

        $file = $driveService->files->create($fileMetadata, array(
            'fields' => 'id'
        ));

        return $file;
    }

    public function searchFolder(string $name)
    {

        $driveService = $this->service;
        $files = array();        
        $pageToken = null;

        do {
            $response = $driveService->files->listFiles(array(
                'q' => "name = '" . $name . "' and mimeType = 'application/vnd.google-apps.folder'",
                'spaces' => 'drive',
                'pageToken' => $pageToken,
                'fields' => 'nextPageToken, files(id, name)',
            ));
            
            array_push($files, $response->files);
            $pageToken = $response->pageToken;
        } while ($pageToken != null);
        
        return $files;
    }

    public function moveFileToFolder($realFileId, $realFolderId)
    {
        $driveService = $this->service;
        // [START drive_move_file_to_folder]
        $fileId = '1sTWaJ_j7PkjzaBWtNc3IzovK5hQf21FbOw9yLeeLPNQ';
        $folderId = '0BwwA4oUTeiV1TGRPeTVjaWRDY1E';
        $emptyFileMetadata = new DriveFile();
        // [START_EXCLUDE silent]
        $fileId = $realFileId;
        $folderId = $realFolderId;
        // [END_EXCLUDE]
        // Retrieve the existing parents to remove
        $file = $driveService->files->get($fileId, array('fields' => 'parents'));
        $previousParents = join(',', $file->parents);
        // Move the file to the new folder
        $file = $driveService->files->update($fileId, $emptyFileMetadata, array(
            'addParents' => $folderId,
            'removeParents' => $previousParents,
            'fields' => 'id, parents'
        ));
        // [END drive_move_file_to_folder]
        return $file->parents;
    }

    public function searchFiles()
    {
        $driveService = $this->service;
        $files = array();
        // [START drive_search_files]
        $pageToken = null;
        do {
            $response = $driveService->files->listFiles(array(
                //'q' => "mimeType='image/jpeg'",
                //'spaces' => 'drive',
                'pageToken' => $pageToken,
                'fields' => 'nextPageToken, files(id, name)',
            ));
            foreach ($response->files as $file) {

                dump($file);
                die;

                printf("Found file: %s (%s)\n", $file->name, $file->id);
            }
            // [START_EXCLUDE silent]
            array_push($files, $response->files);
            // [END_EXCLUDE]

            $pageToken = $response->pageToken;
        } while ($pageToken != null);
        // [END drive_search_files]
        return $files;
    }

    /*public function shareFile($realFileId, $realUser, $realDomain)
    {
        $driveService = $this->service;
        $ids = array();
        // [START drive_share_file]
        $fileId = '1sTWaJ_j7PkjzaBWtNc3IzovK5hQf21FbOw9yLeeLPNQ';
        // [START_EXCLUDE silent]
        $fileId = $realFileId;
        // [END_EXCLUDE]
        $driveService->getClient()->setUseBatch(true);
        try {
            $batch = $driveService->createBatch();

            $userPermission = new Permission(array(
                'type' => 'user',
                'role' => 'writer',
                'emailAddress' => 'user@example.com'
            ));
            // [START_EXCLUDE silent]
            $userPermission['emailAddress'] = $realUser;
            // [END_EXCLUDE]
            $request = $driveService->permissions->create(
                $fileId,
                $userPermission,
                array('fields' => 'id')
            );
            $batch->add($request, 'user');
            $domainPermission = new Permission(array(
                'type' => 'domain',
                'role' => 'reader',
                'domain' => 'example.com'
            ));
            // [START_EXCLUDE silent]
            $userPermission['domain'] = $realDomain;
            // [END_EXCLUDE]
            $request = $driveService->permissions->create(
                $fileId,
                $domainPermission,
                array('fields' => 'id')
            );
            $batch->add($request, 'domain');
            $results = $batch->execute();

            foreach ($results as $result) {
                if ($result instanceof Exception) {
                    // Handle error
                    printf($result);
                } else {
                    printf("Permission ID: %s\n", $result->id);
                    // [START_EXCLUDE silent]
                    array_push($ids, $result->id);
                    // [END_EXCLUDE]
                }
            }
        } finally {
            $driveService->getClient()->setUseBatch(false);
        }
        // [END drive_share_file]
        return $ids;
    }*/

    public function uploadAppData()
    {
        $driveService = $this->service;
        // [START drive_upload_app_data]
        $fileMetadata = new DriveFile(array(
            'name' => 'config.json',
            'parents' => array('appDataFolder')
        ));
        $content = file_get_contents('files/config.json');
        $file = $driveService->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => 'application/json',
            'uploadType' => 'multipart',
            'fields' => 'id'
        ));
        printf("File ID: %s\n", $file->id);
        // [END drive_upload_app_data]
        return $file->id;
    }

    public function listAppData()
    {
        $driveService = $this->service;
        // [START drive_list_app_data]
        $response = $driveService->files->listFiles(array(
            'spaces' => 'appDataFolder',
            'fields' => 'nextPageToken, files(id, name)',
            'pageSize' => 10
        ));
        foreach ($response->files as $file) {
            printf("Found file: %s (%s)", $file->name, $file->id);
        }
        // [END drive_list_app_data]
        return $response->files;
    }

    public function fetchAppDataFolder()
    {
        $driveService = $this->service;
        // [START drive_fetch_app_data_folder]
        $file = $driveService->files->get('appDataFolder', array(
            'fields' => 'id'
        ));
        printf("Folder ID: %s\n", $file->id);
        // [END drive_fetch_app_data_folder]
        return $file->id;
    }

    # TODO - PHP client currently chokes on fetching start page token
    public function fetchStartPageToken()
    {
        $driveService = $this->service;
        # [START drive_fetch_start_page_token]
        $response = $driveService->changes->getStartPageToken();
        printf("Start token: %s\n", $response->startPageToken);
        # [END drive_fetch_start_page_token]
        return $response->startPageToken;
    }

    public function fetchChanges($savedStartPageToken)
    {
        $driveService = $this->service;
        // [START drive_fetch_changes]
        # Begin with our last saved start token for this user or the
        # current token from getStartPageToken()
        $pageToken = $savedStartPageToken;
        while ($pageToken != null) {
            $response = $driveService->changes->listChanges($pageToken, array(
                'spaces' => 'drive'
            ));
            foreach ($response->changes as $change) {
                // Process change
                printf("Change found for file: %s", $change->fileId);
            }
            if ($response->newStartPageToken != null) {
                // Last page, save this token for the next polling interval
                $savedStartPageToken = $response->newStartPageToken;
            }
            $pageToken = $response->nextPageToken;
        }
        // [END drive_fetch_changes]
        return $savedStartPageToken;
    }

}
