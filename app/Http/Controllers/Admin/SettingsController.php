<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Models\PaymentChannel;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index()
    {
        $this->authorize('admin_settings');

        $data = [
            'pageTitle' => trans('admin/main.settings_title'),
        ];

        return view('admin.settings.index', $data);
    }

    public function page($page)
    {
        $this->authorize('admin_settings_' . $page);

        $settings = Setting::where('page', $page)->get()->keyBy('name');

        foreach ($settings as $setting) {
            $setting->value = json_decode($setting->value, true);
        }

        $data = [
            'pageTitle' => trans('admin/main.settings_title'),
            'settings' => $settings
        ];

        if ($page == 'notifications') {
            $data['notificationTemplates'] = NotificationTemplate::all();
        }

        if ($page == 'financial') {
            $paymentChannels = PaymentChannel::orderBy('created_at', 'desc')->paginate(10);
            $data['paymentChannels'] = $paymentChannels;
        }

        return view('admin.settings.' . $page, $data);
    }

    public function store(Request $request, $name)
    {

        if (!empty($request->get('name'))) {
            $name = $request->get('name');
        }

        $values = $request->get('value', null);

        if (!empty($values)) {

            $values = array_filter($values, function ($val) {
                if (is_array($val)) {
                    return array_filter($val);
                } else {
                    return !empty($val);
                }
            });

            $values = json_encode($values);
            $values = str_replace('record', rand(1, 600), $values);

            Setting::updateOrCreate(
                ['name' => $name],
                [
                    'page' => $request->get('page', 'other'),
                    'value' => $values,
                    'updated_at' => time(),
                ]
            );

            cache()->forget($name);
        }

        return back();
    }

    public function storeSeoMetas(Request $request)
    {
        $name = Setting::$seoMetasName;

        $this->authorize('admin_settings_seo');

        $data = $request->all();
        $newValues = $data['value'];
        $values = [];
        $settings = Setting::where('name', $name)->first();

        if (!empty($settings) and !empty($settings->value)) {
            $values = json_decode($settings->value);
        }

        if (!empty($newValues) and !empty($values)) {
            foreach ($newValues as $newKey => $newValue) {
                foreach ($values as $key => $value) {
                    if ($key == $newKey) {
                        $values->$key = $newValue;
                        unset($newValues[$key]);
                    }
                }
            }
        }

        if (!empty($newValues)) {
            $values = array_merge((array)$values, $newValues);
        }

        Setting::updateOrCreate(
            ['name' => $name],
            [
                'page' => 'seo',
                'value' => json_encode($values),
                'updated_at' => time(),
            ]
        );

        cache()->forget($name);

        return back();
    }

    public function editSocials($social_key)
    {
        $this->authorize('admin_settings_general');
        $settings = Setting::where('name', Setting::$socialsName)->first();

        if (!empty($settings)) {
            $values = json_decode($settings->value);

            foreach ($values as $key => $value) {
                if ($key == $social_key) {
                    $data = [
                        'pageTitle' => trans('admin/pages/setting.settings_socials'),
                        'social' => $value,
                        'socialKey' => $social_key,
                    ];

                    return view('admin.settings.general', $data);
                }
            }
        }

        abort(404);
    }

    public function deleteSocials($social_key)
    {
        $this->authorize('admin_settings_general');
        $settings = Setting::where('name', Setting::$socialsName)->first();

        if (!empty($settings)) {
            $values = json_decode($settings->value);
            foreach ($values as $key => $value) {
                if ($key == $social_key) {
                    unset($values->$social_key);
                }
            }

            Setting::updateOrCreate(
                ['name' => Setting::$socialsName],
                [
                    'page' => 'general',
                    'value' => json_encode($values),
                    'updated_at' => time(),
                ]
            );

            cache()->forget(Setting::$socialsName);

            return redirect('/admin/settings/general');
        }

        abort(404);
    }

    public function storeSocials(Request $request)
    {
        $this->authorize('admin_settings_general');
        $this->validate($request, [
            'value.*' => 'required',
        ]);

        $data = $request->all();
        $social = $data['social'];
        $values = [];

        $settings = Setting::where('name', Setting::$socialsName)->first();

        if ($social !== 'newSocial') {
            if (!empty($settings) and !empty($settings->value)) {
                $values = json_decode($settings->value);
                foreach ($values as $key => $value) {
                    if ($key == $social) {
                        $values->$key = $data['value'];
                    }
                }
            }
        } else {
            if (!empty($settings) and !empty($settings->value)) {
                $values = json_decode($settings->value);
            }
            $key = str_replace(' ', '_', $data['value']['title']);
            $newValue[$key] = $data['value'];
            $values = array_merge((array)$values, $newValue);
        }

        Setting::updateOrCreate(
            ['name' => Setting::$socialsName],
            [
                'page' => 'general',
                'value' => json_encode($values),
                'updated_at' => time(),
            ]
        );

        cache()->forget(Setting::$socialsName);

        return redirect('/admin/settings/general');
    }

    public function storeCustomCssJs(Request $request)
    {
        $this->authorize('admin_settings_customization');

        $newValues = $request->get('value', null);
        $values = [];
        $settings = Setting::where('name', Setting::$customCssJsName)->first();

        if (!empty($settings) and !empty($settings->value)) {
            $values = json_decode($settings->value);
        }

        if (!empty($newValues) and !empty($values)) {
            foreach ($newValues as $newKey => $newValue) {
                foreach ($values as $key => $value) {
                    if ($key == $newKey) {
                        $values->$key = $newValue;
                        unset($newValues[$key]);
                    }
                }
            }
        }

        if (!empty($newValues)) {
            $values = array_merge((array)$values, $newValues);
        }

        if (!empty($values)) {
            $values = json_encode($values);

            Setting::updateOrCreate(
                ['name' => Setting::$customCssJsName],
                [
                    'page' => 'customization',
                    'value' => $values,
                    'updated_at' => time(),
                ]
            );

            cache()->forget(Setting::$customCssJsName);

            return back();
        }
    }

    public function notificationsMetas(Request $request)
    {
        $this->authorize('admin_settings_notifications');
        $name = 'notifications';
        $values = $request->get('value', []);

        $settings = Setting::where('name', $name)->first();

        if (!empty($settings) and !empty($settings->value)) {
            $oldValues = json_decode($settings->value, true);

            $values = array_merge($oldValues, $values);
        }

        if (!empty($values)) {
            $values = array_filter($values);
            $values = json_encode($values);

            Setting::updateOrCreate(
                ['name' => $name],
                [
                    'page' => 'notifications',
                    'value' => $values,
                    'updated_at' => time(),
                ]
            );

            cache()->forget($name);
        }

        return back();
    }
}
