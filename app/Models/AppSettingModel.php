<?php

namespace App\Models;

use CodeIgniter\Model;

class AppSettingModel extends Model
{
    protected $table = 'app_settings';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['key', 'value', 'group', 'label', 'type', 'updated_by'];

    public function value(string $key, ?string $default = null): ?string
    {
        $row = $this->where('key', $key)->first();

        return $row['value'] ?? $default;
    }
}
