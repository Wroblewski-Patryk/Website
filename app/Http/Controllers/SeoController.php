<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class SeoController extends Controller
{
    public function sitemap()
    {
        $sitemapEnabled = $this->getSetting('sitemap_enabled', true);

        if (!$sitemapEnabled) {
            abort(404);
        }

        $urls = [];

        // Home
        $urls[] = [
            'loc' => url('/'),
            'lastmod' => now()->toAtomString(),
            'priority' => '1.0',
            'changefreq' => 'daily',
        ];

        // Pages
        Page::where('status', 'published')
            ->where('seo_index', true)
            ->get()
            ->each(function ($page) use (&$urls) {
            $urls[] = [
                'loc' => url($page->slug),
                'lastmod' => $page->updated_at->toAtomString(),
                'priority' => '0.8',
                'changefreq' => 'weekly',
            ];
        });

        // Posts
        Post::where('status', 'published')
            ->where('seo_index', true)
            ->get()
            ->each(function ($post) use (&$urls) {
            // Determine slug (handle PL by default or active locales)
            $slug = is_array($post->slug) ? ($post->slug['pl'] ?? reset($post->slug)) : $post->slug;
            $urls[] = [
                'loc' => url("/blog/{$slug}"),
                'lastmod' => $post->updated_at->toAtomString(),
                'priority' => '0.6',
                'changefreq' => 'weekly',
            ];
        });

        // Projects
        Project::where('status', 'published')
            ->where('seo_index', true)
            ->get()
            ->each(function ($project) use (&$urls) {
            $urls[] = [
                'loc' => url("/projects/{$project->slug}"),
                'lastmod' => $project->updated_at->toAtomString(),
                'priority' => '0.7',
                'changefreq' => 'monthly',
            ];
        });

        // Deduplicate and normalize correctly by path
        $urls = collect($urls)->map(function ($u) {
            $path = parse_url($u['loc'], PHP_URL_PATH) ?: '';
            $u['norm_path'] = trim($path, '/');
            return $u;
        })->unique('norm_path')->map(function ($u) {
            unset($u['norm_path']);
            return $u;
        })->values()->all();

        $content = view('seo.sitemap', compact('urls'))->render();

        return Response::make($content, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots()
    {
        $disallowAdmin = $this->getSetting('robots_disallow_admin', true);
        $sitemapEnabled = $this->getSetting('sitemap_enabled', true);

        $lines = ["User-agent: *"];

        if ($disallowAdmin) {
            $lines[] = "Disallow: /admin/";
        }

        if ($sitemapEnabled) {
            $lines[] = "Sitemap: " . url('/sitemap.xml');
        }

        return Response::make(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
    }

    protected function getSetting($key, $default = null)
    {
        $setting = Setting::where('key', $key)->first();
        if (!$setting)
            return $default;

        $value = $setting->value;
        // If it's localized (array), try to get current or fallback, but for tech SEO booleans it's usually flat
        if (is_array($value) && isset($value['pl'])) {
            return $value['pl'];
        }

        return $value;
    }
}
