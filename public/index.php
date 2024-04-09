<?php
require '../vendor/autoload.php';

use App\FolderFilesService;
use App\Repository\DBFactory;
use App\Repository\FileRepository;
use App\Repository\FolderRepository;
use App\Validator\FolderValidator;

$error = '';
$folder = '';
$filesCollection = [];
$filesCollectionFromDB = [];

try {
    if (isset($_POST['folder'])) {
        $folder = rtrim($_POST['folder'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        FolderValidator::validate($folder);

        $db = DBFactory::get();

        $fileRepository = new FileRepository($db);
        $folderRepository = new FolderRepository($db);
        $filesCollection = (new FolderFilesService($fileRepository, $folderRepository))->getFilesCollectionByFolder($folder);
        $filesCollectionFromDB = $fileRepository->loadFilesByFolderName($folder);

        $db->close();
    }

} catch (\Throwable $e) {
    $error = $e->getMessage();
}

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);
echo $twig->render('index.twig', [
        'error' => $error,
        'folder' => $folder,
        'filesCollection' => $filesCollection,
        'filesCollectionFromDB' => $filesCollectionFromDB
]);