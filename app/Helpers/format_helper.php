<?php

if (! function_exists('money_to_float')) {
    function money_to_float(?string $value): float
    {
        $value = trim((string) $value);
        $value = str_replace(['R$', ' '], '', $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : 0.0;
    }
}

if (! function_exists('only_digits')) {
    function only_digits(?string $value): string
    {
        return preg_replace('/\D+/', '', (string) $value);
    }
}

if (! function_exists('datetime_local_to_sql')) {
    function datetime_local_to_sql(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $value = str_replace('T', ' ', $value);

        return strlen($value) === 16 ? $value . ':00' : $value;
    }
}
