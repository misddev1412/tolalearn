<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class NavbarLinksSettingsController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('admin_additional_pages_navbar_links');
        $this->validate($request, [
            'value.*' => 'required',
        ]);

        $data = $request->all();
        $navbar_link = $data['navbar_link'];
        $values = [];

        $settings = Setting::where('name', Setting::$navbarLinkName)->first();

        if ($navbar_link !== 'newLink') {
            if (!empty($settings) and !empty($settings->value)) {
                $values = json_decode($settings->value);
                foreach ($values as $key => $value) {
                    if ($key == $navbar_link) {
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
            ['name' => Setting::$navbarLinkName],
            [
                'value' => json_encode($values),
                'updated_at' => time(),
            ]
        );

        cache()->forget(Setting::$navbarLinkName);

        return redirect('/admin/additional_page/navbar_links');
    }

    public function edit($link_key)
    {
        $this->authorize('admin_additional_pages_navbar_links');
        $settings = Setting::where('name', Setting::$navbarLinkName)->first();

        if (!empty($settings)) {
            $values = json_decode($settings->value);
            foreach ($values as $key => $value) {
                if ($key == $link_key) {
                    $data = [
                        'pageTitle' => trans('admin/pages/setting.settings_navbar_links'),
                        'navbar_link' => $value,
                        'navbarLinkKey' => $link_key,
                    ];

                    return view('admin.additional_pages.navbar_links', $data);
                }
            }
        }

        abort(404);
    }

    public function delete($link_key)
    {
        $this->authorize('admin_additional_pages_navbar_links');
        $settings = Setting::where('name', Setting::$navbarLinkName)->first();

        if (!empty($settings)) {
            $values = json_decode($settings->value);
            foreach ($values as $key => $value) {
                if ($key == $link_key) {
                    unset($values->$link_key);
                }
            }

            Setting::updateOrCreate(
                ['name' => Setting::$navbarLinkName],
                [
                    'value' => json_encode($values),
                    'updated_at' => time(),
                ]
            );

            cache()->forget(Setting::$navbarLinkName);

            return redirect()->back();
        }

        abort(404);
    }
}
