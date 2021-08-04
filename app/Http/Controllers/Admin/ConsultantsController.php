<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ConsultantsExport;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\ReserveMeeting;
use App\Models\Role;
use App\Models\Sale;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ConsultantsController extends Controller
{
    public function index(Request $request, $exportExcel = false)
    {
        $this->authorize('admin_consultants_lists');

        $query = User::whereIn('role_name', [Role::$teacher, Role::$organization])
            ->join('meetings', 'meetings.creator_id', '=', 'users.id')
            ->leftJoin('group_users', 'users.id', '=', 'group_users.user_id')
            ->leftJoin('sales', function ($join) {
                $join->on('meetings.id', '=', 'sales.meeting_id')
                    ->whereNull('sales.refund_at');
            })
            ->leftJoin('reserve_meetings', function ($join) {
                $join->on('sales.id', '=', 'reserve_meetings.sale_id')
                    ->where('reserve_meetings.status', 'pending');
            })
            ->select('users.*', 'meetings.amount', 'meetings.discount', 'meetings.disabled',
                'sales.seller_id', 'sales.meeting_id', 'sales.refund_at',
                DB::raw('group_users.id as group_id'),
                DB::raw('count(sales.seller_id) as sales_count'),
                DB::raw('sum(sales.total_amount) as sales_amount'),
                DB::raw('(sum(sales.total_amount) - (sum(sales.tax) + sum(sales.commission))) as totalIncome'),
                DB::raw('count(reserve_meetings.sale_id) as pendingAppointments')
            )
            ->groupBy('users.id');

        $totalConsultants = User::whereHas('meeting')->get();

        $availableConsultants = User::whereHas('meeting', function ($query) {
            $query->where('disabled', false);
        })->count();

        $unavailableConsultants = User::whereHas('meeting', function ($query) {
            $query->where('disabled', true);
        })->count();

        $consultantsWithoutAppointment = 0;
        foreach ($totalConsultants as $consultant) {
            $checkConsultantsMeetingSale = Sale::whereNull('refund_at')
                ->where('seller_id', $consultant->id)
                ->whereNotNull('meeting_id')
                ->count();

            if ($checkConsultantsMeetingSale < 1) {
                $consultantsWithoutAppointment += 1;
            }
        }

        $organizations = User::select('id', 'full_name', 'created_at')
            ->where('role_name', Role::$organization)
            ->orderBy('created_at', 'desc')
            ->get();

        $userGroups = Group::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $query = $this->filters($query, $request);

        if ($exportExcel) {
            return $query->with([
                'userGroup'
            ])->get();
        }

        $consultants = $query->with([
            'userGroup'
        ])->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.consultants_list_title'),
            'totalConsultants' => count($totalConsultants),
            'availableConsultants' => $availableConsultants,
            'unavailableConsultants' => $unavailableConsultants,
            'consultantsWithoutAppointment' => $consultantsWithoutAppointment,
            'organizations' => $organizations,
            'userGroups' => $userGroups,
            'consultants' => $consultants,
        ];

        return view('admin.consultants.lists', $data);
    }

    private function filters($query, $request)
    {
        $from = $request->get('from', null);
        $to = $request->get('to', null);
        $search = $request->get('search', null);
        $sort = $request->get('sort', null);
        $organization_id = $request->get('organization_id', null);
        $group_id = $request->get('group_id', null);
        $disabled = $request->get('disabled', null);

        $query = fromAndToDateFilter($from, $to, $query, 'users.created_at');

        if (!empty($search)) {
            $query->where('users.full_name', 'like', "%$search%");
        }

        if (!empty($sort)) {
            switch ($sort) {
                case 'appointments_asc':
                    $query->orderBy('sales_count', 'asc');
                    break;
                case 'appointments_desc':
                    $query->orderBy('sales_count', 'desc');
                    break;
                case 'appointments_income_asc':
                    $query->orderBy('totalIncome', 'asc');
                    break;
                case 'appointments_income_desc':
                    $query->orderBy('totalIncome', 'desc');
                    break;
                case 'pending_appointments_asc':
                    $query->orderBy('pendingAppointments', 'asc');
                    break;
                case 'pending_appointments_desc':
                    $query->orderBy('pendingAppointments', 'desc');
                    break;
                case 'created_at_asc':
                    $query->orderBy('users.created_at', 'asc');
                    break;
                case 'created_at_desc':
                    $query->orderBy('users.created_at', 'desc');
                    break;
            }
        }

        if (!empty($organization_id)) {
            $query->where('organ_id', $organization_id);
        }

        if (!empty($group_id)) {
            $query->where('group_id', $group_id);
        }

        if (isset($disabled)) {
            $query->where('disabled', ($disabled == '1') ? 1 : 0);
        }

        return $query;
    }

    public function exportExcel(Request $request)
    {
        $this->authorize('admin_consultants_export_excel');

        $consultants = $this->index($request, true);

        $exports = new ConsultantsExport($consultants);

        return Excel::download($exports, 'consultants.xlsx');
    }
}
