<?php

namespace App\Http\Controllers;

use App\Models\PlacementAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
    /**
     * View or download an attachment.
     *
     * @param  \App\Models\PlacementAttachment  $attachment
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function viewOrDownload(PlacementAttachment $attachment, Request $request)
    {
        // Define the disk where files are stored (e.g., 'private' or 'local' if storage/app)
        $diskName = config('filesystems.default_private_disk', 'private'); // Use a config value or default to 'private'

        if (!Storage::disk($diskName)->exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }

        $path = Storage::disk($diskName)->path($attachment->file_path);
        $filename = $attachment->original_filename;
        $mimeType = $attachment->mime_type ?: Storage::disk($diskName)->mimeType($attachment->file_path);

        // If 'preview=true' is in query string and it's an image, try to display inline
        if ($request->query('preview') === 'true' && $attachment->type === 'image' && Str::startsWith($mimeType, 'image/')) {
            return response()->file($path, ['Content-Type' => $mimeType]);
        }

        // For other cases or if not a preview, force download
        return Storage::disk($diskName)->download($attachment->file_path, $filename);
    }
}