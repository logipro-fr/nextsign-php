<?php

namespace NextSignPHP\Domain\Model\DTO;

use InvalidArgumentException;
use JsonSerializable;
use Symfony\Component\Filesystem\Path;

use function Safe\file;

class Document implements JsonSerializable
{
    public function __construct(
        private string $type,
        private string $content,
        private string $name
    )
    {
        
    }

    public function jsonSerialize(): mixed
    {
        return [
            "type" => $this->type,
            "content" => $this->content,
            "name" => $this->name
        ];
    }

    public static function fromPath(string $filepath): Document
    {
        switch (Path::getExtension($filepath, true)) {
            case 'pdf':
                $type = "pdf";
                break;
            default:
                throw new InvalidArgumentException("type " . Path::getExtension($filepath, true) .
                " is not recognized");
        }
        $name = Path::getFilenameWithoutExtension($filepath, '');
        $content = base64_encode(implode(file($filepath)));
        return new Document($type, $content, $name);
    }
}
