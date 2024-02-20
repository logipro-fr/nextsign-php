<?php

namespace NextSignPHP\Domain\Model\DTO;

use InvalidArgumentException;
use JsonSerializable;
use Symfony\Component\Filesystem\Path;

use function Safe\file;

class Document implements JsonSerializable
{
    private string $type;
    private string $content;
    private string $name;
    public function __construct(string $filepath)
    {
        switch (Path::getExtension($filepath, true)) {
            case 'pdf':
                $this->type = "pdf";
                break;
            default:
                throw new InvalidArgumentException("type " . Path::getExtension($filepath, true) .
                " is not recognized");
        }
        $this->name = Path::getFilenameWithoutExtension($filepath, '');
        $this->content = base64_encode(implode(file($filepath)));
    }

    public function jsonSerialize(): mixed
    {
        return [
            "type" => $this->type,
            "content" => $this->content,
            "name" => $this->name
        ];
    }
}
