<?php

namespace App\Models;

use App\Models\CustomerFeedback;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerFeedbackExport implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {
        return CustomerFeedback::all();
    }

    public function headings(): array
    {
        return [
            'id',
            'user_id',
            'product',
            'price',
            'delivery'
        ];
    }

    public function map($feedback): array
    {
        return [

            $feedback->id,
            $feedback->user_id,
            $feedback->product,
            $feedback->price,
            $feedback->delivery
        ];
    }
}
