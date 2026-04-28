<?php

namespace EvolutionCMS\BlogDaisyui\Controllers;

use EvolutionCMS\Models\SiteContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class BaseController
{
    protected $evo;
    protected array $themeSettings = [];
    public array $data = [];

    public function __construct()
    {
        $this->evo = EvolutionCMS();
        $this->themeSettings = $this->themeSettings();

        ksort($_GET);
        $cacheId = sha1(json_encode([
            'doc' => (int) $this->evo->documentIdentifier,
            'get' => $_GET,
            'theme' => $this->themeSettingsHash(),
        ]));

        if ($this->evo->getConfig('enable_cache')) {
            $this->data = Cache::rememberForever($cacheId, function () {
                $this->globalElements();
                $this->render();

                return $this->data;
            });
        } else {
            $this->globalElements();
            $this->render();
        }

        $this->noCacheRender();
        $this->sendToView();
    }

    public function render(): void
    {
    }

    public function noCacheRender(): void
    {
    }

    public function globalElements(): void
    {
        $this->data['menu'] = $this->menuTree();
        $this->data['themeConfig'] = $this->themeConfig();
        $this->data['parentIds'] = SiteContent::ancestorsWithSelfOf($this->evo->documentIdentifier)
            ->pluck('id')
            ->toArray();
    }

    protected function themeConfig(): array
    {
        $config = $this->themeSettings;

        $light = $this->themeGroup($config['light'] ?? []);
        $dark = $this->themeGroup($config['dark'] ?? []);

        $defaultLight = $this->themeDefault($config['default_light'] ?? null, $light, 'light');
        $defaultDark = $this->themeDefault($config['default_dark'] ?? null, $dark, 'dark');

        return [
            'enabled' => (bool) ($config['enabled'] ?? true),
            'showToggle' => (bool) ($config['show_toggle'] ?? true),
            'showThemes' => (bool) ($config['show_themes'] ?? true),
            'defaultLight' => $defaultLight,
            'defaultDark' => $defaultDark,
            'storageKey' => trim((string) ($config['storage_key'] ?? 'evo.blogDaisyui.theme')),
            'light' => $light,
            'dark' => $dark,
        ];
    }

    protected function themeGroup(array $themes): array
    {
        $out = [];

        foreach ($themes as $name => $label) {
            if (is_int($name)) {
                $name = $label;
            }

            $name = trim((string) $name);
            $label = trim((string) $label);

            if ($name === '') {
                continue;
            }

            $out[] = [
                'name' => $name,
                'label' => $label !== '' ? $label : $name,
            ];
        }

        return $out;
    }

    protected function themeDefault(?string $theme, array $group, string $fallback): string
    {
        $theme = trim((string) $theme);
        $names = array_column($group, 'name');

        if ($theme !== '' && in_array($theme, $names, true)) {
            return $theme;
        }

        return $names[0] ?? $fallback;
    }

    protected function themeSettings(): array
    {
        $config = config('presets.blog-daisyui.theme', config('blog-daisyui.theme', []));

        return is_array($config) ? $config : [];
    }

    protected function themeSettingsHash(): string
    {
        $payload = json_encode($this->themeSettings, JSON_UNESCAPED_SLASHES);

        return sha1($payload !== false ? $payload : serialize($this->themeSettings));
    }

    protected function menuTree(): array
    {
        $tree = SiteContent::GetRootTree(2)
            ->where('site_content.hidemenu', 0)
            ->orderBy('t2.menuindex')
            ->orderBy('t2.id')
            ->get()
            ->unique('id')
            ->toTree()
            ->toArray();

        return $this->visibleMenuItems($tree);
    }

    protected function visibleMenuItems(array $items): array
    {
        $visible = [];

        foreach ($items as $item) {
            if ((bool) ($item['hidemenu'] ?? false)) {
                continue;
            }

            $children = $this->visibleMenuItems($item['children'] ?? []);
            if ($children === []) {
                unset($item['children']);
            } else {
                $item['children'] = $children;
            }

            $visible[] = $item;
        }

        return $visible;
    }

    protected function currentDocument(): ?SiteContent
    {
        $id = (int) $this->evo->documentIdentifier;

        return $id > 0 ? SiteContent::query()->find($id) : null;
    }

    protected function blogRoot(): ?SiteContent
    {
        return SiteContent::query()
            ->where('parent', 0)
            ->where('alias', 'blog')
            ->where('deleted', 0)
            ->first();
    }

    protected function blogPostsQuery(?int $parentId = null): Builder
    {
        $query = SiteContent::query()
            ->where('deleted', 0)
            ->where('published', 1)
            ->where('isfolder', 0);

        if ($parentId) {
            $query->where('parent', $parentId);
        } else {
            $query->whereRaw('1 = 0');
        }

        return $query
            ->orderByRaw('CASE WHEN pub_date > 0 THEN pub_date WHEN publishedon > 0 THEN publishedon ELSE createdon END DESC')
            ->orderByDesc('id');
    }

    protected function latestPosts(int $limit = 3): array
    {
        $blog = $this->blogRoot();

        if (!$blog) {
            return [];
        }

        return $this->blogPostsQuery((int) $blog->id)
            ->limit($limit)
            ->get()
            ->map(fn (SiteContent $post) => $this->mapPost($post))
            ->all();
    }

    protected function mapPost(SiteContent $post): array
    {
        return [
            'id' => (int) $post->id,
            'title' => (string) $post->pagetitle,
            'longtitle' => (string) ($post->longtitle ?: $post->pagetitle),
            'summary' => $this->documentSummary($post),
            'date' => $this->formatDate($this->documentTimestamp($post)),
            'url' => $this->evo->makeUrl((int) $post->id),
        ];
    }

    protected function documentSummary(SiteContent $document, int $limit = 180): string
    {
        $summary = trim((string) ($document->introtext ?: $document->description));

        if ($summary === '') {
            $summary = trim(preg_replace('/\s+/', ' ', strip_tags((string) $document->content)) ?? '');
        }

        if (mb_strlen($summary) <= $limit) {
            return $summary;
        }

        return rtrim(mb_substr($summary, 0, $limit - 1)) . '...';
    }

    protected function documentTimestamp(SiteContent $document): int
    {
        foreach (['pub_date', 'publishedon', 'createdon', 'editedon'] as $field) {
            $timestamp = (int) $document->{$field};

            if ($timestamp > 0) {
                return $timestamp;
            }
        }

        return 0;
    }

    protected function formatDate(int $timestamp): string
    {
        return $timestamp > 0 ? date('M j, Y', $timestamp) : '';
    }

    protected function pageUrl(int $documentId, int $page = 1): string
    {
        return $this->evo->makeUrl($documentId, '', $page > 1 ? http_build_query(['page' => $page]) : '');
    }

    protected function absoluteUrl(string $url): string
    {
        if (preg_match('~^https?://~i', $url)) {
            return $url;
        }

        $siteUrl = rtrim((string) $this->evo->getConfig('site_url'), '/');
        $path = $url !== '' && $url[0] === '/' ? $url : '/' . ltrim($url, '/');

        return $siteUrl . $path;
    }

    public function sendToView(): void
    {
        $this->evo->addDataToView($this->data);
    }
}
