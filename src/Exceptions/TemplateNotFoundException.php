<?php

declare(strict_types=1);

namespace  Dinho\TemplateEngine\Exceptions;

use Exception;

class TemplateNotFoundException extends Exception
{
    public static function make(string $template): self
    {
        return new self("File '$template' not found.");
    }
}
