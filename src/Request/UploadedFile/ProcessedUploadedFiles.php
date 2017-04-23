<?php

declare(strict_types=1);

namespace Purist\Http\Request\UploadedFile;

final class ProcessedUploadedFiles implements UploadedFiles
{
    private $uploadedFilesParams;

    public function __construct(array $uploadedFilesParams)
    {
        $this->uploadedFilesParams = $uploadedFilesParams;
    }

    public function toArray(): array
    {
        $uploadedFiles = [];

        foreach ($this->uploadedFilesParams as $inputName => $file) {
            $uploadedFiles[$inputName] = is_array($file['tmp_name'])
                ? $this->nestedUploadedFiles($file)
                : new UploadedFile(
                    $file['name'],
                    $file['type'],
                    $file['size'],
                    $file['tmp_name'],
                    $file['error']
                );
        }

        return $uploadedFiles;
    }

    private function nestedUploadedFiles(array $file)
    {
        return array_reduce(
            array_keys($file['tmp_name']),
            function($inputNameKey, $carry) use ($file) {
                $carry[$inputNameKey] = new UploadedFile(
                    $file['name'][$inputNameKey],
                    $file['type'][$inputNameKey],
                    $file['size'][$inputNameKey],
                    $file['tmp_name'][$inputNameKey],
                    $file['error'][$inputNameKey]
                );

                return $carry;
            },
            []
        );
    }
}
