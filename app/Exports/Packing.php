<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Packing implements FromCollection, WithHeadings, ShouldAutoSize
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
            'No Dokumen',
            'Jumlah Faktur',
            'Kode Dealer',
            'Tanggal',
            'Kode Packer',
            'No Meja',
            'Waktu Mulai',
            'Waktu Selesai',
            'Waktu Proses',
        ];
    }
}
