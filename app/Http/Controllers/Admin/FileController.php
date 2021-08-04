<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class FileController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('admin_webinars_edit');

        $data = $request->all();

        if (empty($data['storage'])) {
            $data['storage'] = 'local';
        }

        if (!empty($data['file_path']) and is_array($data['file_path'])) {
            $data['file_path'] = $data['file_path'][0];
        }

        $validator = Validator::make($data, [
            'webinar_id' => 'required',
            'title' => 'required|max:64',
            'accessibility' => 'required|' . Rule::in(File::$accessibility),
            'file_path' => 'required',
            'file_type' => 'required_if:storage,online',
            'volume' => 'required_if:storage,online',
            'description' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!empty($data['webinar_id'])) {
            $webinar = Webinar::where('id', $data['webinar_id'])->first();

            if (!empty($webinar)) {
                //$teacher = $webinar->creator;

                $volumeMatches = [''];
                $fileInfos = null;
                if ($data['storage'] == 'local') {
                    $fileInfos = $this->fileInfo($data['file_path']);
                } else {
                    preg_match('!\d+!', $data['volume'], $volumeMatches);
                }

                File::create([
                    'creator_id' => $webinar->creator_id,
                    'webinar_id' => $data['webinar_id'],
                    'title' => $data['title'],
                    'file' => $data['file_path'],
                    'volume' => formatSizeUnits(!empty($fileInfos) ? $fileInfos['size'] : ($volumeMatches[0] * 1048576)),
                    'file_type' => !empty($fileInfos) ? $fileInfos['extension'] : $data['file_type'],
                    'accessibility' => $data['accessibility'],
                    'storage' => $data['storage'],
                    'downloadable' => !empty($data['downloadable']) ? true : false,
                    'description' => $data['description'],
                    'created_at' => time()
                ]);

                return response()->json([
                    'code' => 200,
                ], 200);
            }
        }

        return response()->json([], 422);
    }


    public function edit(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $file = File::where('id', $id)->first();

        if (!empty($file)) {
            $file->file_path = $file->file;

            return response()->json([
                'file' => $file
            ], 200);
        }

        return response()->json([], 422);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $data = $request->all();

        if (empty($data['storage'])) {
            $data['storage'] = 'local';
        }

        if (!empty($data['file_path']) and is_array($data['file_path'])) {
            $data['file_path'] = $data['file_path'][0];
        }

        $validator = Validator::make($data, [
            'webinar_id' => 'required',
            'title' => 'required|max:64',
            'accessibility' => 'required|' . Rule::in(File::$accessibility),
            'file_path' => 'required',
            'file_type' => 'required_if:storage,online',
            'volume' => 'required_if:storage,online',
            'description' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $volumeMatches = ['0'];
        $fileInfos = null;
        if ($data['storage'] == 'local') {
            $fileInfos = $this->fileInfo($data['file_path']);
        } else {
            preg_match('!\d+!', $data['volume'], $volumeMatches);
        }

        $file = File::where('id', $id)->first();

        if (!empty($file)) {
            $file->update([
                'title' => $data['title'],
                'file' => $data['file_path'],
                'volume' => formatSizeUnits(!empty($fileInfos) ? $fileInfos['size'] : ($volumeMatches[0] * 1048576)),
                'file_type' => !empty($fileInfos) ? $fileInfos['extension'] : $data['file_type'],
                'accessibility' => $data['accessibility'],
                'storage' => $data['storage'],
                'downloadable' => !empty($data['downloadable']) ? true : false,
                'description' => $data['description'],
                'updated_at' => time()
            ]);

            return response()->json([
                'code' => 200,
            ], 200);
        }

        return response()->json([], 422);
    }

    public function fileInfo($path)
    {
        $file = array();

        $file_path = public_path($path);

        $filePath = pathinfo($file_path);

        $file['name'] = $filePath['filename'];
        $file['extension'] = $filePath['extension'];
        $file['size'] = filesize($file_path);

        return $file;
    }

    public function destroy(Request $request, $id)
    {
        $this->authorize('admin_webinars_edit');

        $file = File::where('id', $id)
            ->first();

        if (!empty($file)) {
            $file->delete();
        }

        return redirect()->back();
    }
}
