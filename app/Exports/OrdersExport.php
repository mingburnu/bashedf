<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    private Collection $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function collection(): Collection
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [
            trans('ui.merchant'),
            trans('ui.payee'),
            trans('ui.transacted_at'),
            trans('validation.attributes.order_id'),
            trans('ui.order'),
            trans('validation.attributes.amount'),
            trans('validation.attributes.processing_fee'),
            trans('validation.attributes.status'),
        ];
    }
}
