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

        return $this->parseUploadedFiles($this->uploadedFiles);
    }

    /**
     * @return array Where leafs of the array are UploadedFile
     */
    private function parseUploadedFiles(array $uploadedFiles)
    {
        if (!array_key_exists('tmp_name', $uploadedFiles)) {
            return array_map([$this, __METHOD__], $uploadedFiles);
        }

        if (is_array($uploadedFiles['tmp_name'])) {
            return array_map(
                function ($key) use ($uploadedFiles) {
                    return $this->parseUploadedFiles(
                        [
                            'name' => $uploadedFiles['name'][$key],
                            'type' => $uploadedFiles['type'][$key],
                            'size' => $uploadedFiles['size'][$key],
                            'tmp_name' => $uploadedFiles['tmp_name'][$key],
                            'error' => $uploadedFiles['error'][$key],
                        ]
                    );
                },
                array_keys($uploadedFiles['tmp_name'])
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
}
