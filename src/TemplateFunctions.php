<?php
declare(strict_types=1);

namespace Dinho\TemplateEngine;

use Dinho\TemplateEngine\Template;

class TemplateFunctions
{
    private array $block = [];

    public function __construct(private readonly string $path, private readonly string $content, private readonly array $data)
    {

    }

    public function functions(): string
    {
        $content = $this->content;
        $data = $this->data;
        $content = $this->extendHolder($content);
        return $this->replaceForeach($content, $data);
    }

    private function extendHolder(string $content):string
    {
        return preg_replace_callback('/\[%(\s*EXTENDS\s*)(\w+\.php)\s*%]/s', function ($matches){
            $file = $this->path .DIRECTORY_SEPARATOR. $matches[2];
            return file_get_contents($file);
        } ,$content);
    }

    private function replaceForeach(string $content, array $data): string
    {
        return preg_replace_callback('/\[%\s*foreach\s+(.*?)\s+as\s+(.*?)\s*%]\s*(.*?)\s*\[%\s*endforeach\s*%]/s', function ($matches) use ($content, $data) {
            $variableName = $matches[1];
            $replacedVariable = $matches[2];
            $loopContent = $matches[3];
            $replacement = '';

            foreach ($data[$variableName] as $items)
            {
                $contentStorage = [$items];
                foreach ($contentStorage as $item)
                {
                    $variable = '[% '. $replacedVariable .' %]';
                    $replacedContent = str_replace($variable, $item, $loopContent);
                    $replacement .= $replacedContent;
                }
            }
            $output = str_replace($matches[0] ,$replacement, $content);
            return $output;
        }, $content);
    }
}