<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class Retur implements FromCollection, WithHeadings, ShouldAutoSize
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
        // return [
        //     'No Dokumen',
        //     'Kode Part',
        //     'Tgl Klaim',
        //     'Tgl Klaim SPV',
        //     'Tgl Retur Supplier',
        //     'Tgl Jawab terakhir',
        //     'Kode Salse',
        //     'Kode Dealer',
        //     'Kode Supplier',
        //     'Status Stock',
        //     'Status Minimum',
        //     'Status Klaim',
        //     'Status Approve SPV',
        //     'Setatus Retur Selesai',
        //     'keterangan',
        //     'QTY Klaim',
        //     'QTY Dijawab',
        //     'QTY Ganti Barang Diterima',
        //     'QTY Ganti Barang Ditolak',
        //     'QTY Ganti Uang Terima',
        //     'QTY Ganti Uang Tolak',
        //     'Total Ganti Uang'
        // ];
        return [
            'kode Dealer',
            'Nama Dealer',
            'Tipe',
            'qty Klaim',
            'qty Jawab',
            'tanggal klaim',
            'tanggal pakai',
            'pemakaian',
            'Uraian Klaim',
        ];
    }
}
