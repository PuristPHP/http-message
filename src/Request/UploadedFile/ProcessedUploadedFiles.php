<?php
declare(strict_types=1);

namespace Purist\Http\Request\UploadedFile;

use InvalidArgumentException;
use Psr\Http\Message\UploadedFileInterface;

final class ProcessedUploadedFiles implements UploadedFiles
{
    private $uploadedFiles;

    public function __construct(array $processedUploadedFiles)
    {
        array_walk_recursive($processedUploadedFiles, [$this, 'assertUploadedFile']);

        $this->uploadedFiles = $processedUploadedFiles;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function assertUploadedFile($leaf): void
    {
        if (!$leaf instanceof UploadedFileInterface) {
            throw new InvalidArgumentException(
                sprintf(
                    'Leafs of valid uploaded files array needs to be instance of %s',
                    UploadedFileInterface::class
                )
            );
        }
    }
}
