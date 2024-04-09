<?php

namespace Tests\unit;

use App\Entity\File;
use PHPUnit\Framework\TestCase;
use App\Repository\FileRepositoryInterface;
use App\Repository\FolderRepositoryInterface;
use App\FolderFilesService;

class FolderFilesServiceTest extends TestCase
{
    private int $folderId = 1;

    /**
     * @test
     * @dataProvider findFileToChangeByNameDataProvider
     *
     * @return void
     */
    public function testFindFileToChangeByName(File $file, int|null|bool $expected): void
    {
        $fileCollection = $this->getFileCollection();

        $fileRepository = $this->getMockBuilder(FileRepositoryInterface::class)
            ->getMock();
        $folderRepository = $this->getMockBuilder(FolderRepositoryInterface::class)
            ->getMock();

        $folderFilesService = new FolderFilesService($fileRepository, $folderRepository);

        $reflection = new \ReflectionClass($folderFilesService);
        $method = $reflection->getMethod('findFileToChangeByName');
        $method->setAccessible(true);

        $result = $method->invoke($folderFilesService, $fileCollection, $file);

        $this->assertEquals($expected, $result);
    }

    private function getFileCollection(): array
    {
        return [
            new File(1, $this->folderId, 'DBFactory.php', 857, 1712644663),
            new File(2, $this->folderId, 'FileRepository.php', 1960, 1712641427),
            new File(3, $this->folderId, 'FileRepositoryInterface.php', 333, 1712640265),
            new File(4, $this->folderId, 'FolderRepository.php', 1291, 1712641305),
            new File(5, $this->folderId, 'FolderRepositoryInterface.php', 291, 1712641305),
        ];
    }

    public function findFileToChangeByNameDataProvider(): array
    {
        return [
            [new File(null, $this->folderId, 'DBFactory.php', 856, 1712655208), 1],
            [new File(null, $this->folderId, 'DBFactory.php', 857, 1712644663), false],
            [new File(null, $this->folderId, 'SomeFile.php', 123, 1712675663), null]
        ];
    }
}