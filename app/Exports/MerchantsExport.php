<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MerchantsExport implements FromCollection, WithHeadings
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
            trans('ui.merchant_name'),
            trans('ui.successful_count'),
            trans('ui.deposit') . trans('validation.attributes.total_amount'),
            trans('ui.payment') . trans('validation.attributes.total_amount')
        ];
    }
}
