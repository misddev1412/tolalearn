<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentChannel;
use Illuminate\Http\Request;

class PaymentChannelController extends Controller
{
    public function index()
    {
        $this->authorize('admin_payment_channel_list');

        $paymentChannels = PaymentChannel::orderBy('created_at', 'desc')->paginate(10);

        $data = [
            'pageTitle' => trans('admin/pages/paymentChannels.payment_channels'),
            'paymentChannels' => $paymentChannels
        ];

        return view('admin.settings.financial.payment_channel.lists', $data);
    }

    public function edit($id)
    {
        $this->authorize('admin_payment_channel_edit');

        $paymentChannel = PaymentChannel::findOrFail($id);

        $data = [
            'pageTitle' => trans('admin/pages/paymentChannels.payment_channel_edit'),
            'paymentChannel' => $paymentChannel
        ];

        return view('admin.settings.financial.payment_channel.create', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_payment_channel_edit');

        $this->validate($request, [
            'title' => 'required',
        ]);

        $data = $request->all();
        $paymentChannel = PaymentChannel::findOrFail($id);

        $paymentChannel->update([
            'title' => $data['title'],
            'image' => $data['image'],
            'status' => $data['status'],
        ]);

        return redirect('/admin/settings/financial');
    }

    public function toggleStatus($id)
    {
        $this->authorize('admin_payment_channel_toggle_status');

        $channel = PaymentChannel::findOrFail($id);

        $channel->update([
            'status' => ($channel->status == 'active') ? 'inactive' : 'active'
        ]);

        return redirect('/admin/settings/financial');
    }
}
