<?php

namespace EvolutionCMS\BlogDaisyui\Controllers;

use EvolutionCMS\Models\SiteContent;
use Illuminate\Support\Facades\Cache;

class BaseController
{
    protected $evo;
    public array $data = [];

    public function __construct()
    {
        $this->evo = EvolutionCMS();

        ksort($_GET);
        $cacheId = sha1(json_encode([
            'doc' => (int) $this->evo->documentIdentifier,
            'get' => $_GET,
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
        $this->data['parentIds'] = SiteContent::ancestorsWithSelfOf($this->evo->documentIdentifier)
            ->pluck('id')
            ->toArray();
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
