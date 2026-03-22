<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        // Scan storage/app/public/media directly
        $this->scanDirectory('media');
    }

    private function scanDirectory($directory)
    {
        $files = Storage::disk('public')->files($directory);
        foreach ($files as $path) {
            $fullPath = Storage::disk('public')->path($path);
            if (File::exists($fullPath)) {
                $file = new \Symfony\Component\HttpFoundation\File\File($fullPath);
                $this->registerFile($file, $path);
            }
        }
    }

    private function registerFile($file, $relativeRepoPath)
    {
        $extension = strtolower($file->getExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'mp4', 'pdf'];

        if (!in_array($extension, $allowedExtensions)) {
            return;
        }

        // Check if already exists to avoid duplicates
        if (Media::where('path', $relativeRepoPath)->exists()) {
            return;
        }

        Media::create([
            'path' => $relativeRepoPath,
            'mime' => $this->getMimeType($extension),
            'size' => $file->getSize(),
            'alt_text' => Str::title(str_replace(['-', '_'], ' ', $file->getBasename('.' . $file->getExtension()))),
            'folder_id' => null,
        ]);
    }

    private function getMimeType($extension)
    {
        $mimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            'mp4' => 'video/mp4',
            'pdf' => 'application/pdf',
        ];

        return $mimes[$extension] ?? 'application/octet-stream';
    }
}
