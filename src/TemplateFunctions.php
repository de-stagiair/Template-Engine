<?php
declare(strict_types=1);

namespace Dinho\TemplateEngine;

class TemplateFunctions
{
    public function __construct(private readonly string $path, private readonly string $content, private readonly array $data)
    {

    }

    public function functions(): string
    {
        $content = $this->content;
        $data = $this->data;

        return $this->extendHolder($content);
    }

    private function extendHolder(string $content):string
    {
        return preg_replace_callback('/\[%(\s*EXTENDS\s*)(\w+\.php)\s*%]/s', function ($matches){
            $file = $this->path .DIRECTORY_SEPARATOR. $matches[2];
            return file_get_contents($file);
        } ,$content);
    }
}