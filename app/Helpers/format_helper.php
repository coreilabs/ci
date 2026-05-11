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

if (! function_exists('human_date')) {
    function human_date(?string $value): string
    {
        if (! $value) {
            return '-';
        }

        $timestamp = strtotime($value);

        if (! $timestamp) {
            return '-';
        }

        $date = date('Y-m-d', $timestamp);
        if ($date === date('Y-m-d')) {
            return 'Hoje';
        }
        if ($date === date('Y-m-d', strtotime('+1 day'))) {
            return 'Amanhã';
        }
        if ($date === date('Y-m-d', strtotime('-1 day'))) {
            return 'Ontem';
        }

        return date('d/m/Y', $timestamp);
    }
}

if (! function_exists('human_datetime')) {
    function human_datetime(?string $value): string
    {
        if (! $value) {
            return '-';
        }

        $timestamp = strtotime($value);

        if (! $timestamp) {
            return '-';
        }

        return human_date($value) . ' às ' . date('H:i', $timestamp);
    }
}

if (! function_exists('human_time')) {
    function human_time(?string $value): string
    {
        if (! $value) {
            return '-';
        }

        $timestamp = strtotime($value);

        return $timestamp ? date('H:i', $timestamp) : '-';
    }
}

if (! function_exists('human_month')) {
    function human_month(?string $value): string
    {
        if (! $value) {
            return '-';
        }

        $timestamp = strtotime(strlen($value) === 7 ? $value . '-01' : $value);

        return $timestamp ? date('m/Y', $timestamp) : '-';
    }
}
