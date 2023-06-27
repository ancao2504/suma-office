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
            'No. Retur',
            'Tanggal Retur',
            'No. Faktur',
            'Tanggal Faktur',
            'Kode Sales',
            'Kode Dealer',
            'Kode Part',
            'Qty Claim',
            'Qty Dikirim',
            'Keterangan',
            'Status'
        ];
    }
}
