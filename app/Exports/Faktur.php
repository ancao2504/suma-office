<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Faktur implements FromCollection, WithHeadings, ShouldAutoSize
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
            'Company',
            'No Faktur',
            'Tgl Faktur',
            'SPV',
            'Kd Sales',
            'Kd Dealer',
            'Nm Dealer',
            'Kota',
            'Kd Produk',
            'Kd Sub',
            'Kd Part',
            'Jml Order',
            'Jml Jual',
            'Total'
        ];
    }
}
