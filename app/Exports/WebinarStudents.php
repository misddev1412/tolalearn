<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class WebinarStudents implements FromCollection, WithHeadings, WithMapping
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->sales;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            trans('admin/pages/users.full_name'),
            trans('admin/main.email'),
            trans('panel.purchase_date'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($sale): array
    {
        return [
            $sale->buyer->full_name,
            $sale->buyer->email,
            dateTimeFormat($sale->created_at, 'Y M j | H:i')
        ];
    }
}
