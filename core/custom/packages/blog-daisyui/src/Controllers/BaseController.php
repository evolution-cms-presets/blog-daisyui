<?php

namespace EvolutionCMS\BlogDaisyui\Controllers;

use EvolutionCMS\Models\SiteContent;
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
        return SiteContent::GetRootTree(2)
            ->where('site_content.hidemenu', 0)
            ->orderBy('t2.menuindex')
            ->orderBy('t2.id')
            ->get()
            ->unique('id')
            ->toTree()
            ->toArray();
    }

    public function sendToView(): void
    {
        $this->evo->addDataToView($this->data);
    }
}
