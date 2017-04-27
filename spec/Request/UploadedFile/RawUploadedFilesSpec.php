<?php

namespace spec\Purist\Http\Request\UploadedFile;

use Purist\Http\Request\UploadedFile\RawUploadedFiles;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Purist\Http\Request\UploadedFile\UploadedFile;
use Purist\Http\Request\UploadedFile\UploadedFiles;

class RawUploadedFilesSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([
            'inputName' => [
                'name' => 'index.html',
                'type' => 'text/html',
                'size' => 500,
                'tmp_name' => '/tmp/SAkakekA',
                'error' => UPLOAD_ERR_OK,
            ],
        ]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RawUploadedFiles::class);
        $this->shouldImplement(UploadedFiles::class);
    }

    function it_returns_array_of_uploaded_file()
    {
        $this->toArray()->shouldHaveKeyWithType(['inputName', UploadedFile::class]);
    }

    function it_returns_nested_array_of_uploaded_file()
    {
        $this->beConstructedWith([
            'nestedInputName' => [
                'anotherInputName' => [
                    'deepInputName' => [
                        'name' => 'index.html',
                        'type' => 'text/html',
                        'size' => 500,
                        'tmp_name' => '/tmp/SAkakekA',
                        'error' => UPLOAD_ERR_OK,
                    ],
                ],
            ],
        ]);

        $this->toArray()->shouldHaveNestedKeysWithType(
            [
                ['nestedInputName', 'anotherInputName', 'deepInputName'],
                UploadedFile::class
            ]
        );
    }

    function it_returns_nested_array_of_multiple_uploaded_files()
    {
        $this->beConstructedWith([
            'nestedInputName' => [
                'anotherInputName' => [
                    'deepInputName' => [
                        'name' => ['index.html', 'readme.txt'],
                        'type' => ['text/html', 'text/plain'],
                        'size' => [500, 100],
                        'tmp_name' => ['/tmp/SAkakekA', '/tmp/bAeaAkyE'],
                        'error' => [UPLOAD_ERR_OK, UPLOAD_ERR_OK],
                    ],
                ],
            ],
        ]);

        $this->toArray()->shouldHaveNestedKeysWithTypes(
            [
                ['nestedInputName', 'anotherInputName', 'deepInputName'],
                [UploadedFile::class, UploadedFile::class]
            ]
        );
    }

    public function getMatchers()
    {
        return [
            'haveKeyWithType' => function ($subject, array $keyType) {
                list($key, $type) = $keyType;
                return $subject[$key] instanceof $type;
            },
            'haveNestedKeysWithType' => function ($subject, array $keysType) {
                list($keys, $type) = $keysType;
                $nestedSubject =& $subject;

                foreach ($keys as $key) {
                    $nestedSubject =& $nestedSubject[$key];
                }

                return $nestedSubject instanceof $type;
            },
            'haveNestedKeysWithTypes' => function ($subject, array $keysTypes) {
                list($keys, $types) = $keysTypes;
                $nestedSubject =& $subject;

                foreach ($keys as $key) {
                    $nestedSubject =& $nestedSubject[$key];
                }

                return array_filter(
                    $nestedSubject,
                    function ($value, $key) use ($types) {
                        return $value instanceof $types[$key];
                    },
                    ARRAY_FILTER_USE_BOTH
                ) === $nestedSubject;
            },
        ];
    }
}
