<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $users;
    protected $currency;

    public function __construct($users)
    {
        $this->users = $users;
        $this->currency = currencySign();
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->users;
    }

    /**
     * @inheritDoc
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Mobile',
            'Email',
            'Classes',
            'Appointments',
            'Wallet Charge',
            'User Group',
            'Register Date',
            'Status',
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($user): array
    {
        return [
            $user->id,
            $user->full_name,
            $user->mobile,
            $user->email,
            $user->classesPurchasedsCount . '(' . $this->currency . $user->classesPurchasedsSum . ')',
            $user->meetingsPurchasedsCount . '(' . $this->currency . $user->meetingsPurchasedsSum . ')',
            $this->currency . $user->getAccountingBalance(),
            !empty($user->userGroup) ? $user->userGroup->group->name : '',
            dateTimeFormat($user->created_at, 'Y/m/j - H:i'),
            $user->status,
        ];
    }
}
