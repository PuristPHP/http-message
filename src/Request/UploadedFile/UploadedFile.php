<?php
declare(strict_types=1);

namespace Purist\Http\Request\UploadedFile;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Purist\Http\Stream\LazyStream;
use RuntimeException;

final class UploadedFile implements UploadedFileInterface
{
    private $name;
    private $type;
    private $size;
    private $tmpName;
    private $error;

    public function __construct(
        ?string $name,
        ?string $type,
        ?int $size,
        ?string $tmpName,
        int $error
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
        $this->tmpName = $tmpName;
        $this->error = $error;
    }

    /**
     * @inheritdoc
     */
    public function getStream(): StreamInterface
    {
        if (!is_uploaded_file($this->tmpName)) {
            throw new RuntimeException(
                sprintf('%s is not an uploaded file', $this->tmpName)
            );
        }

        return new LazyStream($this->tmpName, 'r+');
    }

    /**
     * @inheritdoc
     */
    public function moveTo($targetPath): void
    {
        if (!is_uploaded_file($this->tmpName)) {
            throw new RuntimeException(
                sprintf('%s is not an uploaded file', $this->tmpName)
            );
        }

        if (move_uploaded_file($this->tmpName, $targetPath) === false) {
            throw new InvalidArgumentException(
                sprintf('Could not move %s to %s', $this->tmpName, $targetPath)
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @inheritdoc
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @inheritdoc
     */
    public function getClientFilename(): ?string
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getClientMediaType(): ?string
    {
        return $this->type;
    }
}
