<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CancelListsExport implements FromCollection, WithMapping, WithHeadings
{

    public function collection()
    {
        return CustomerCancelList::all();
    }

    public function headings(): array
    {
       return [
        'date',
        'total_order',
        'cancel',
        'delivery',
        'nextday',
        'processing',
        'notes',
       ];
    }

    public function map($customer): array
    {
        return [
            $customer->date,
            $customer->total_order,
            $customer->cancel,
            $customer->delivery,
            $customer->nextday,
            $customer->processing,
            $customer->notes,
        ];
    }
}


