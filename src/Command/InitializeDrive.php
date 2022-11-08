<?php

namespace App\Command;

use App\Entity\DriveMetaData;
use App\Helper\Constants;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use App\Services\GoogleDrive;
use Doctrine\Persistence\ManagerRegistry;

#[AsCommand(
    name: 'app:initialize-drive',
    description: 'Resource to initialize google drive application folders.',
    hidden: false,
)]
class InitializeDrive extends Command
{

    private $googleDriveService;
    private $doctrine;
    private $em;

    public function __construct(GoogleDrive $service, ManagerRegistry $doctrine)
    {
        parent::__construct();
        
        $this->googleDriveService = $service;
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getManager();
    }



    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $this->checkFolder(Constants::DRIVE_FOLDERS_HIERARCHY);
        return Command::SUCCESS;

    }

    private function checkFolder (array $folder, ?string $parentId = null) 
    {

        $name = isset($folder['name']) ? $folder['name'] . ($_ENV['APP_ENV'] === 'dev' ? '_dev' : '' ) : null;
        $dataType = isset($folder['dataType']) ? $folder['dataType'] : null;
        $children = isset($folder['children']) && is_array($folder['children']) ? $folder['children'] : null;
       
        if (!$name || !$dataType) {
            return;
        }

        $q = ['folderName'=>$name];
        
        if ($parentId) {
            $q['parentId'] = $parentId;
        }

        $entity = $this->em->getRepository(DriveMetaData::class)->findOneBy($q);

        if (!$entity) {
            $metadata = $this->googleDriveService->createFolder($name, $parentId);
            $driveId = $this->createDriveMetaData($name, $parentId, $dataType, $metadata);            
        } else {
            $driveId = $entity->getDriveId();
        }

        if ($children) {
            foreach ($children as $child) {
                $this->checkFolder($child, $driveId);
            }
        }

    }

    private function createDriveMetaData($name, $parentId, $dataType, $metadata) {
        $driveId = $metadata->id;            

        $folder = (array)$metadata;
        $metadata = [];

        foreach($folder as $k => $val) {
            if (strpos($k, "\x00*\x00") === false) {
                $metadata[$k] = $val;
            }
        }

        $entity = new DriveMetaData();
        $entity->setDriveId($driveId);
        $entity->setFolderName($name);            
        $entity->setDataType($dataType);
        $entity->setMetaData($metadata);
        $entity->setParentId($parentId);

        $this->em->persist($entity);
        $this->em->flush();

        return $driveId;
    }
}
