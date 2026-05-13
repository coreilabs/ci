<?php

namespace App\Models;

use CodeIgniter\Model;

class ContractModel extends Model
{
    protected $table = 'contracts';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['treatment_id', 'title', 'body_snapshot', 'pdf_path', 'signed_file_path', 'created_by', 'updated_by'];
}
