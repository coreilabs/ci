<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentTemplateModel extends Model
{
    protected $table = 'document_templates';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'category', 'body', 'version', 'is_required_admission'];
}
