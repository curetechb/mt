<?php
namespace App\Models;

use App\Models\CustomerComplain;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerComplainExport implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {
        return CustomerComplain::all();
    }

    public function headings(): array
    {
        return [
            'id',
            'order_id',
            'description',
            'status'
        ];
    }

    public function map($complain): array
    {
       return [
        $complain->id,
        $complain->order_id,
        $complain->description,
        $complain->status

       ];

    }
}
