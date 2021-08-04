<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\File as FacadeFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Validator;

class FileController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->get('ajax')['new'];

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
            'description' => 'nullable|max:255',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $volumeMatches = [''];
        $fileInfos = null;
        if ($data['storage'] == 'local') {
            $fileInfos = $this->fileInfo($data['file_path']);
        } else {
            preg_match('!\d+!', $data['volume'], $volumeMatches);
        }

        File::create([
            'creator_id' => $user->id,
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

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data = $request->get('ajax')[$id];

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
            'description' => 'nullable|max:255',
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

        $file = File::where('id', $id)
            ->where('creator_id', $user->id)
            ->first();

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
        $file = File::where('id', $id)
            ->where('creator_id', auth()->id())
            ->first();

        if (!empty($file)) {
            $file->delete();
        }

        return response()->json([
            'code' => 200
        ], 200);
    }
}
