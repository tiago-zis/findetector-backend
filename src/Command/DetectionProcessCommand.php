<?php

namespace App\Command;

use App\Entity\DetectionProcessError;
use App\Entity\Image;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputOption;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:detection-process')]
class DetectionProcessCommand extends Command
{

    use LockableTrait;

    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // ...
            ->addOption(
                'image-id',
                null,
                InputOption::VALUE_REQUIRED,
                'Image ID to execute the object detection process.',
                0
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return Command::SUCCESS;
        }

        $imageId = (int)$input->getOption('image-id');
        $by = $imageId ? ['id' => $imageId] : ['status' => 'uploaded'];
        $connParams = $this->em->getConnection()->getParams();
        $connString = "host='" . $connParams['host'] . "' dbname='" . $connParams['dbname'] . "' user='" . $connParams['user'] . "' password='" . $connParams['password'] . "'";
        $condaEnv = $_ENV['CONDA_ENV'];
        $script = $_ENV['DETECTION_SCRIPT_PATH'];       

        $list = $this->em->getRepository(Image::class)->findBy($by, ['createdAt' => 'ASC'], 5, 0);

        foreach ($list as $image) {
            $result = $this->executeObjectDetection($image, $condaEnv, $script, $connString);

            if (!$result) {
                $image->setStatus('uploaded');
                $this->em->persist($image);
                $this->em->flush();
            }
        }

        return Command::SUCCESS;
    }

    public function executeObjectDetection(Image $image, $condaEnv, $script, $connString)
    {

        $table = "data.image";
        $filePath = $_ENV['FILE_UPLOAD_PATH'] . $image->getFile()->getName();
        $id = $image->getId();

        $image->setStatus('processing');
        $this->em->persist($image);
        $this->em->flush();

        $command = 'conda run -n ' . $condaEnv . ' --no-capture-output python ' . $script .
            '  --file="' . $filePath . '" --conn_string="' . $connString . '" --table_name="' . $table . '" --record_id=' . $id;

        $output = null;
        exec($command, $output);

        if (!is_array($output) || count($output) === 0) {
            return false;
        }

        $data = json_decode($output[0], true);

        if ($data && (!isset($data['boxes']) || !isset($data['scores']))) {
            $this->em->persist(new DetectionProcessError($image, 'Detection process without boxes or scores', $data));
            $this->em->flush();
            return false;
        } else if (!$data) {
            $this->em->persist(new DetectionProcessError($image, 'Detection process error', $output));
            $this->em->flush();
            return false;
        }

        return true;
    }
}
