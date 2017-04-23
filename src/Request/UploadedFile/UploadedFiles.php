<?php

declare(strict_types=1);

namespace Purist\Request\UploadedFile;

interface UploadedFiles
{
    public function toArray(): array;
}
