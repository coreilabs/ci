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

if (! function_exists('money_br')) {
    function money_br(float $value): string
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }
}

if (! function_exists('only_digits')) {
    function only_digits(?string $value): string
    {
        return preg_replace('/\D+/', '', (string) $value);
    }
}

if (! function_exists('cpf_br')) {
    function cpf_br(?string $value): string
    {
        $cpf = only_digits($value);
        if (strlen($cpf) !== 11) {
            return $cpf ?: '-';
        }

        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }
}

if (! function_exists('phone_br')) {
    function phone_br(?string $value): string
    {
        $phone = only_digits($value);
        if (strlen($phone) === 11) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7);
        }
        if (strlen($phone) === 10) {
            return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6);
        }

        return $phone ?: '-';
    }
}

if (! function_exists('cep_br')) {
    function cep_br(?string $value): string
    {
        $cep = only_digits($value);
        if (strlen($cep) !== 8) {
            return $cep ?: '-';
        }

        return substr($cep, 0, 5) . '-' . substr($cep, 5);
    }
}

if (! function_exists('is_valid_cpf')) {
    function is_valid_cpf(?string $value): bool
    {
        $cpf = only_digits($value);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($i = 0; $i < $t; $i++) {
                $sum += (int) $cpf[$i] * (($t + 1) - $i);
            }

            $digit = ((10 * $sum) % 11) % 10;
            if ((int) $cpf[$t] !== $digit) {
                return false;
            }
        }

        return true;
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

if (! function_exists('date_br')) {
    function date_br(?string $value): string
    {
        if (! $value) {
            return '-';
        }

        $timestamp = strtotime($value);

        return $timestamp ? date('d/m/Y', $timestamp) : '-';
    }
}

if (! function_exists('month_name_ptbr')) {
    function month_name_ptbr(int $month): string
    {
        $names = [
            1 => 'janeiro',
            2 => 'fevereiro',
            3 => 'março',
            4 => 'abril',
            5 => 'maio',
            6 => 'junho',
            7 => 'julho',
            8 => 'agosto',
            9 => 'setembro',
            10 => 'outubro',
            11 => 'novembro',
            12 => 'dezembro',
        ];

        return $names[$month] ?? '';
    }
}

if (! function_exists('date_long_ptbr')) {
    function date_long_ptbr(?string $value): string
    {
        if (! $value) {
            return '-';
        }

        $timestamp = strtotime($value);
        if (! $timestamp) {
            return '-';
        }

        return date('d', $timestamp) . ' de ' . month_name_ptbr((int) date('n', $timestamp)) . ' de ' . date('Y', $timestamp);
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

if (! function_exists('number_to_words_ptbr')) {
    function number_to_words_ptbr(int $number): string
    {
        if ($number === 0) {
            return 'zero';
        }

        $belowTwenty = [
            0 => '',
            1 => 'um',
            2 => 'dois',
            3 => 'três',
            4 => 'quatro',
            5 => 'cinco',
            6 => 'seis',
            7 => 'sete',
            8 => 'oito',
            9 => 'nove',
            10 => 'dez',
            11 => 'onze',
            12 => 'doze',
            13 => 'treze',
            14 => 'quatorze',
            15 => 'quinze',
            16 => 'dezesseis',
            17 => 'dezessete',
            18 => 'dezoito',
            19 => 'dezenove',
        ];
        $tens = [
            2 => 'vinte',
            3 => 'trinta',
            4 => 'quarenta',
            5 => 'cinquenta',
            6 => 'sessenta',
            7 => 'setenta',
            8 => 'oitenta',
            9 => 'noventa',
        ];
        $hundreds = [
            1 => 'cento',
            2 => 'duzentos',
            3 => 'trezentos',
            4 => 'quatrocentos',
            5 => 'quinhentos',
            6 => 'seiscentos',
            7 => 'setecentos',
            8 => 'oitocentos',
            9 => 'novecentos',
        ];

        $chunkToWords = static function (int $chunk) use ($belowTwenty, $tens, $hundreds): string {
            if ($chunk === 0) {
                return '';
            }
            if ($chunk === 100) {
                return 'cem';
            }

            $parts = [];
            $hundred = intdiv($chunk, 100);
            $rest = $chunk % 100;

            if ($hundred > 0) {
                $parts[] = $hundreds[$hundred];
            }

            if ($rest > 0) {
                if ($rest < 20) {
                    $parts[] = $belowTwenty[$rest];
                } else {
                    $ten = intdiv($rest, 10);
                    $unit = $rest % 10;
                    $parts[] = $unit > 0 ? $tens[$ten] . ' e ' . $belowTwenty[$unit] : $tens[$ten];
                }
            }

            return implode(' e ', $parts);
        };

        $millions = intdiv($number, 1000000);
        $thousands = intdiv($number % 1000000, 1000);
        $rest = $number % 1000;
        $parts = [];

        if ($millions > 0) {
            $parts[] = $millions === 1 ? 'um milhão' : number_to_words_ptbr($millions) . ' milhões';
        }

        if ($thousands > 0) {
            $parts[] = $thousands === 1 ? 'mil' : $chunkToWords($thousands) . ' mil';
        }

        if ($rest > 0) {
            $parts[] = $chunkToWords($rest);
        }

        return implode(' e ', $parts);
    }
}

if (! function_exists('money_to_words_ptbr')) {
    function money_to_words_ptbr(float $value): string
    {
        $reais = (int) floor($value);
        $centavos = (int) round(($value - $reais) * 100);
        if ($centavos === 100) {
            $reais++;
            $centavos = 0;
        }

        $parts = [];
        $parts[] = number_to_words_ptbr($reais) . ' ' . ($reais === 1 ? 'real' : 'reais');

        if ($centavos > 0) {
            $parts[] = number_to_words_ptbr($centavos) . ' ' . ($centavos === 1 ? 'centavo' : 'centavos');
        }

        return implode(' e ', $parts);
    }
}
