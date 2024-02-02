<?php
declare(strict_types=1);

namespace Dinho\TemplateEngine;

class TemplateFunctions
{
    private array $block = [];

    public function __construct(private readonly string $path, private readonly string $content, private readonly array $data)
    {

    }

    public function functions(): string
    {
        ini_set("xdebug.var_display_max_children", '-1');
        ini_set("xdebug.var_display_max_data", '-1');
        ini_set("xdebug.var_display_max_depth", '-1');

        $content = $this->content;
        $data = $this->data;
        $content = $this->replaceForeach($content, $data);
        $this->getBlocks($content);
        return $this->replaceBlock($content);
    }

    private function getBlocks(string $content): array
    {
        preg_match_all('/\[% BLOCK (.*?) %](.*?)\[% ENDBLOCK %]/s', $content, $matches);
        foreach ($matches[0] as $index => $match){
            $this->block[$matches[1][$index]] = $matches[2][$index];
        }
        return $this->block;
    }
    private function replaceBlock(string $content): string
    {
        foreach ($this->block as $key => $value) {
            $placeholder = "[%" . " BLOCK $key %" . "]";
            $endPlaceholder = "[%" . " ENDBLOCK %" . "]";
            $startPosition = strpos($content, $placeholder);
            $endPosition = strpos($content, $endPlaceholder, $startPosition);

            if ($startPosition !== false && $endPosition !== false) {
                $replacePart = substr($content, $startPosition, $endPosition - $startPosition + strlen($endPlaceholder));
                $content = str_replace($replacePart, $value, $content);
                var_dump($content);
            }
        }
        //var_dump($content);
        return $content;
    }


    private function replaceForeach(string $content, array $data): string
    {
        return preg_replace_callback('/\[%\s*foreach\s+(.*?)\s+as\s+(.*?)\s*%](.*?)\[%\s*endforeach\s*%]/s', function ($matches) use ($data) {
            $variableName = $matches[1];
            $replacedVariable = $matches[2];
            $loopContent = $matches[3];
            $replacement = '';

            foreach ($data[$variableName] as $item) {
                $variable = '[% ' . $replacedVariable . ' %]';
                $replacedContent = str_replace($variable, $item, $loopContent);
                $replacement .= $replacedContent;
            }
            return $replacement;
        }, $content);
    }
}