<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accounting;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Sale;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin_documents_list');

        $documents = Accounting::query();
        $users = User::whereNull('deleted_at')->get()->keyBy('id');

        $from = $request->input('from');
        $to = $request->input('to');
        $user = $request->input('user');
        $webinar = $request->input('webinar');
        $type = $request->input('type');
        $typeAccount = $request->input('type_account');

        $documents = fromAndToDateFilter($from, $to, $documents, 'created_at');

        if (isset($user)) {
            $userIds = $user;
            $documents->whereIn('user_id', $userIds);
        }

        $webinarModel = '';
        if (isset($webinar)) {
            $documents->whereIn('webinar_id', [$webinar]);
            $webinarModel = Webinar::where('id', $webinar)->first();
        }

        if (isset($type) && $type !== 'all') {
            $documents->where('type', $type);
        }

        if (isset($typeAccount) && $typeAccount !== 'all') {
            $documents->where('type_account', $typeAccount);
        }

        $documents = $documents->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/financial.document_page_title'),
            'documents' => $documents,
            'users' => $users,
            'webinar' => $webinarModel,
        ];

        return view('admin.financial.documents.lists', $data);
    }


    public function create()
    {
        $this->authorize('admin_documents_create');

        $data = [
            'pageTitle' => trans('admin/pages/financial.new_document_page_title'),
        ];

        return view('admin.financial.documents.new', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_documents_create');

        $this->validate($request, [
            'amount' => 'required',
            'user_id' => 'required|exists:users,id',
            'type' => 'required',
        ]);

        $data = $request->all();

        Accounting::create([
            'creator_id' => auth()->user()->id,
            'amount' => $data['amount'],
            'user_id' => $data['user_id'],
            'type' => $data['type'],
            'description' => $data['description'],
            'type_account' => Accounting::$asset,
            'store_type' => Accounting::$storeManual,
            'created_at' => time(),
        ]);

        $notifyOptions = [
            '[f.d.type]' => $data['type'],
            '[amount]' => $data['amount'],
        ];

        sendNotification('new_financial_document', $notifyOptions, $data['user_id']);

        return redirect('/admin/financial/documents');
    }

    public function printer($id)
    {
        $this->authorize('admin_documents_print');

        $document = Accounting::findOrFail($id);

        $data = [
            'document' => $document
        ];

        return view('admin.financial.documents.print', $data);
    }
}
