<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerListExport implements FromCollection, WithMapping, WithHeadings
{
    public function collection(){
        return User::where('user_type', 'customer')->orderBy('created_at', 'desc')->get();
    }

    public function headings() : array
    {
        return [
            'Code',
            'Name',
            'Address',
            'VATRegistrationNo',
            'ContactPersonName',
            'ContactPersonAddress',
            'Mobile',
            'Email',
            'Fax',
            'Web',
            'ErrorMessage'
        ];

    }

    public function map($user): array
    {
        $address = $user->addresses()->first();


        return [
            $user->id,
            $user->name,
            $address->address ?? " ",
            $user-> null,
            $user->name ,
            $address->address ?? " ",
            $user->phone,
            $user->email,
            $user-> null,
            $user-> null,
            $user->null
        ];
    }

}
