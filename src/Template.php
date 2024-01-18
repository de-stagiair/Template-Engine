<?php
declare(strict_types=1);

namespace Dinho\TemplateEngine;

use Dinho\TemplateEngine\Exceptions\TemplateNotFoundException;

class Template
{
    public function __construct(private readonly string $path)
    {

    }

    /**
     * @throws TemplateNotFoundException
     */
    public function render(string $template, array $data = []): void
    {
        $path = $this->getPath($template);
        $this->checkPath($path);
        $content = $this->getContent($path);
        $functions = new TemplateFunctions($this->path, $content, $data);
        $content = $functions->functions();
        $content = $this->getData($content, $data);
        echo $content;
    }

    private function getPath(string $template):string
    {
        return $this->path .DIRECTORY_SEPARATOR. $template;
    }

    /**
     * @throws TemplateNotFoundException
     */
    private function checkPath(string $path): void
    {
        if (!file_exists($path)) {
            throw TemplateNotFoundException::make($path);
        }
    }

    private function getContent(string $path): string
    {
        return file_get_contents($path);
    }
    private function getData(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            $content = str_replace("[% $key %]", $value, $content);
        }
        return $content;
    }
}