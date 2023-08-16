<?php

namespace App\Exports\Retur;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Konsumen implements FromCollection, WithHeadings, ShouldAutoSize
{
    private $data;
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    public function collection(){
        ini_set('memory_limit', '4096M');
        ini_set('max_execution_time', '0');
        $data = $this->data;
        
        return collect($data);
    }


    public function headings(): array
    {
        return [
            'no_klaim',
            'kd_part',
            'tgl_klaim',
            'tgl_approve',
            'tgl_retur',
            'tgl_jwb',
            'kd_dealer',
            'kd_sales',
            'kd_supp',
            'sts_klaim',
            'sts_stock',
            'sts_min',
            'keterangan',
            'qty_klaim',
            'qty_jwb'
        ];
    }
}
