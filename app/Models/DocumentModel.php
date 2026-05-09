<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table = 'documents';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'treatment_id', 'template_id', 'category', 'name', 'body_snapshot',
        'file_path', 'signed_file_path', 'version',
    ];
}
