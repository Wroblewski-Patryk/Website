<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

use App\Models\MediaFolder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class MediaController extends Controller
{
    /**
     * MIME types accepted after server-side content sniffing.
     *
     * @var array<int, string>
     */
    private array $allowedUploadMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'application/pdf',
        'video/mp4',
        'application/zip',
        'application/x-zip-compressed',
        'multipart/x-zip',
    ];

    public function index(Request $request)
    {
        $query = Media::query();

        $viewType = $request->get('view_type', 'nested');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function (Builder $q) use ($search) {
                $q->where('path', 'like', "%{$search}%")
                    ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }
        elseif ($viewType === 'flat') {
        // No folder filtering for flat view
        }
        else {
            $query->where('folder_id', $request->get('folder_id'));
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSorts = ['created_at', 'size', 'path'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $media = $query->paginate(40)->withQueryString();
        $folders = MediaFolder::with('children')->get();
        $currentFolder = $request->folder_id ?MediaFolder::find($request->folder_id) : null;

        // Subfolders for navigation (only if nested)
        $subfolders = $viewType === 'nested'
            ?MediaFolder::where('parent_id', $request->get('folder_id'))->get()
            : collect([]);

        $data = [
            'media' => $media,
            'folders' => $folders,
            'subfolders' => $subfolders,
            'currentFolder' => $currentFolder,
            'filters' => $request->only(['search', 'folder_id', 'sort', 'direction', 'view_type'])
        ];

        if ($request->wantsJson()) {
            return $this->jsonSuccess($data, 'media.index_success');
        }

        return Inertia::render('Admin/Media/Index', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'files.*' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp,pdf,mp4,zip', 'max:51200'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'folder_id' => ['nullable', 'exists:media_folders,id']
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $this->validateUploadedFile($file);
                $path = $file->store('media', 'public');

                Media::create([
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'alt_text' => $request->alt_text,
                    'folder_id' => $request->folder_id
                ]);
            }
        }

        return redirect()->back()->with('success', 'media.upload_success');
    }

    private function validateUploadedFile(UploadedFile $file): void
    {
        if (!$file->isValid() || $file->getSize() <= 0) {
            throw ValidationException::withMessages([
                'files' => ['One of the uploaded files is invalid.'],
            ]);
        }

        $detectedMimeType = (string) $file->getMimeType();
        if (!in_array($detectedMimeType, $this->allowedUploadMimeTypes, true)) {
            throw ValidationException::withMessages([
                'files' => ['One of the uploaded files has an unsupported MIME type.'],
            ]);
        }
    }

    public function update(Request $request, Media $media)
    {
        $request->validate([
            'alt_text' => ['nullable', 'string', 'max:255'],
            'folder_id' => ['nullable', 'exists:media_folders,id'],
            'filename' => ['nullable', 'string', 'max:255']
        ]);

        $data = $request->only(['alt_text', 'folder_id']);

        // Renaming logic
        if ($request->filled('filename')) {
            $oldPath = $media->path;
            $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
            $newFilename = str($request->filename)->slug() . '.' . $extension;
            $newPath = 'media/' . $newFilename;

            if ($oldPath !== $newPath && !Storage::disk('public')->exists($newPath)) {
                Storage::disk('public')->move($oldPath, $newPath);
                $data['path'] = $newPath;
            }
        }

        $media->update($data);

        return redirect()->back()->with('success', 'media.update_success');
    }

    public function storeFolder(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:media_folders,id']
        ]);

        MediaFolder::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id
        ]);

        return redirect()->back()->with('success', 'media.folder_create_success');
    }

    public function updateFolder(Request $request, MediaFolder $folder)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:media_folders,id']
        ]);

        $folder->update([
            'name' => $request->name,
            'slug' => str($request->name)->slug(),
            'parent_id' => $request->has('parent_id') ? $request->parent_id : $folder->parent_id
        ]);

        return redirect()->back()->with('success', 'media.folder_update_success');
    }

    public function destroyFolder(MediaFolder $folder)
    {
        $folder->delete();
        return redirect()->back()->with('success', 'media.folder_delete_success');
    }

    public function destroy(Media $media)
    {
        if ($media->path && Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }
        $media->delete();

        return redirect()->back()->with('success', 'media.delete_success');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'media_ids' => ['nullable', 'array'],
            'folder_ids' => ['nullable', 'array'],
            'action' => ['required', 'string', 'in:delete,move'],
            'target_folder_id' => ['nullable', 'exists:media_folders,id']
        ]);

        $mediaIds = $request->input('media_ids', []);
        $folderIds = $request->input('folder_ids', []);

        if ($request->action === 'delete') {
            $media = Media::whereIn('id', $mediaIds)->get();
            foreach ($media as $item) {
                if ($item->path && Storage::disk('public')->exists($item->path)) {
                    Storage::disk('public')->delete($item->path);
                }
                $item->delete();
            }

            MediaFolder::whereIn('id', $folderIds)->delete();

            return redirect()->back()->with('success', 'media.bulk_delete_success');
        }

        if ($request->action === 'move') {
            $targetFolderId = $request->input('target_folder_id');

            Media::whereIn('id', $mediaIds)->update(['folder_id' => $targetFolderId]);
            MediaFolder::whereIn('id', $folderIds)->update(['parent_id' => $targetFolderId]);

            return redirect()->back()->with('success', 'media.bulk_move_success');
        }

        return redirect()->back();
    }
}
