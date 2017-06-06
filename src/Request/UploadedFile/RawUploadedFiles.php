<?php
declare(strict_types=1);

namespace Purist\Http\Request\UploadedFile;

final class RawUploadedFiles implements UploadedFiles
{
    private $uploadedFiles;

    public function __construct(array $uploadedFiles = [])
    {
        $this->uploadedFiles = $uploadedFiles;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        if (empty($this->uploadedFiles)) {
            return [];
        }

        return $this->processUploadedFiles($this->uploadedFiles);
    }

    /**
     * Recursive method for processing uploaded files array from $_FILES
     *
     * @return array Where leafs of the array are UploadedFile
     */
    private function processUploadedFiles(array $uploadedFiles)
    {
        if ($this->isNested($uploadedFiles)) {
            return array_map([$this, __METHOD__], $uploadedFiles);
        }

        if ($this->isArrayOfFiles($uploadedFiles)) {
            return array_map(
                [$this, __METHOD__],
                $this->normalizeMultipleUploadedFiles($uploadedFiles)
            );
        }

        return new UploadedFile(
            $uploadedFiles['name'],
            $uploadedFiles['type'],
            $uploadedFiles['size'],
            $uploadedFiles['tmp_name'],
            $uploadedFiles['error']
        );
    }

    private function normalizeMultipleUploadedFiles(array $uploadedFiles): array
    {
        return array_map(
            function (int $key) use ($uploadedFiles) {
                return [
                    'name' => $uploadedFiles['name'][$key],
                    'type' => $uploadedFiles['type'][$key],
                    'size' => $uploadedFiles['size'][$key],
                    'tmp_name' => $uploadedFiles['tmp_name'][$key],
                    'error' => $uploadedFiles['error'][$key],
                ];
            },
            $this->fileIndexes($uploadedFiles)
        );
    }

    private function isArrayOfFiles(array $uploadedFiles): bool
    {
        return is_array($uploadedFiles['tmp_name']);
    }

    private function fileIndexes(array $uploadedFiles): array
    {
        return array_keys($uploadedFiles['tmp_name']);
    }

    private function isNested(array $uploadedFiles): bool
    {
        return !array_key_exists('tmp_name', $uploadedFiles);
    }
}
