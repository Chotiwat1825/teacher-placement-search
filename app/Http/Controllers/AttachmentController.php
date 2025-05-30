<?php

namespace App\Http\Controllers;

use App\Models\PlacementAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;

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
        $diskName = config('filesystems.default_private_disk', 'private');

        if (!Storage::disk($diskName)->exists($attachment->file_path)) {
            abort(404, 'File not found.');
        }

        $path = Storage::disk($diskName)->path($attachment->file_path);
        $filename = $attachment->original_filename;
        $mimeType = $attachment->mime_type ?: Storage::disk($diskName)->mimeType($attachment->file_path);

        // ถ้ามี parameter 'download=true' หรือไม่ใช่รูปภาพ ให้บังคับดาวน์โหลด
        if ($request->query('download') === 'true' || !($attachment->type === 'image' && \Str::startsWith($mimeType, 'image/'))) {
            return Storage::disk($diskName)->download($attachment->file_path, $filename);
        }

        // ถ้าเป็นรูปภาพ และไม่มี 'download=true' (สำหรับ Lightbox หรือ preview) ให้แสดง inline
        // response()->file() จะตั้ง Content-Disposition เป็น 'inline' โดยอัตโนมัติสำหรับ MimeType ที่รู้จัก
        return response()->file($path, ['Content-Type' => $mimeType]);
    }
}
