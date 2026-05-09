<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = db_connect();

        $data = [
            'activeTreatments' => $db->table('treatments')->where('status', 'active')->countAllResults(),
            'openCharges' => $db->table('financial_entries')->where('status', 'open')->countAllResults(),
            'lateCharges' => $db->table('financial_entries')
                ->where('status', 'open')
                ->where('due_date <', date('Y-m-d'))
                ->countAllResults(),
            'monthRevenue' => (float) ($db->table('financial_entries')
                ->selectSum('amount')
                ->where('status', 'paid')
                ->where('competence', date('Y-m'))
                ->get()
                ->getRow('amount') ?? 0),
        ];

        return view('dashboard/index', $data);
    }
}
