<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrganizationsExport implements FromCollection, WithHeadings, WithMapping
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
            'Classes Sales',
            'Appointments Sales',
            'Purchased Classes',
            'Purchased Appointments',
            'Teachers',
            'Students',
            'Register Date',
            'Status',
            'Verified',
            'Ban',
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
            $user->classesSalesCount . '(' . $this->currency . $user->classesSalesSum . ')',
            $user->meetingsSalesCount . '(' . $this->currency . $user->meetingsSalesSum . ')',
            $user->classesPurchasedsCount . '(' . $this->currency . $user->classesPurchasedsSum . ')',
            $user->meetingsPurchasedsCount . '(' . $this->currency . $user->meetingsPurchasedsSum . ')',
            $user->getOrganizationTeachers()->count(),
            $user->getOrganizationStudents()->count(),
            dateTimeFormat($user->created_at, 'Y/m/j - H:i'),
            $user->status,
            $user->verified ? 'Yes' : 'No',
            ($user->ban and !empty($user->ban_end_at) and $user->ban_end_at > time()) ? ('Yes ' . '(Until ' . dateTimeFormat($user->ban_end_at, 'Y/m/j') . ')') : 'No',
        ];
    }
}
