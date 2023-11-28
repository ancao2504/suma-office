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

        return collect($data)->map(function($item) {
            return [
                $item->kd_dealer,
                $item->nm_dealer,
                $item->kd_sales,
                $item->kd_part,
                $item->no_klaim,
                $item->qty_klaim,
                $item->qty_jwb,
                $item->tgl_klaim,
                $item->tgl_pakai,
                $item->pemakaian,
                $item->keterangan,
                $item->sts_stock,
                $item->sts_min,
                $item->sts_klaim,
                $item->sts_approve,
                $item->sts_selesai
            ];
        });
    }


    public function headings(): array
    {
        return [
            'Kode Dealer',
            'Nama Dealer',
            'Kode Sales',
            'Part Number',
            'Nomer Dokumen',
            'QTY Klaim',
            'QTY Jawab',
            'Tanggal Klaim',
            'Tanggal Pakai',
            'pemakaian (Hari)',
            'keterangan',
            'Status Stock',
            'Status Minimum',
            'Status Klaim',
            'Status Approve',
            'Status Jawab'
        ];
    }
}
