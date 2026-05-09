<?php

namespace App\Libraries;

class TemplateRenderer
{
    public function render(string $html, array $vars): string
    {
        foreach ($vars as $key => $value) {
            $html = str_replace('{{' . $key . '}}', (string) $value, $html);
        }

        return $html;
    }
}
