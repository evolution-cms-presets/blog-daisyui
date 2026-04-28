<?php

namespace EvolutionCMS\BlogDaisyui\Seeders;

use EvolutionCMS\Models\SiteContent;
use EvolutionCMS\Models\SiteTemplate;
use Illuminate\Database\Seeder;

class HomeTemplateSeeder extends Seeder
{
    private const PROJECT_GITIGNORE = <<<'GITIGNORE'
# Evo core and manager are installed separately and are not part of this project layer.
/core/*
!/core/custom/
!/core/custom/**
/manager/
/install/

# Root files produced by the Evo installer.
/index.php
/.htaccess
/composer.json
/composer.lock
/vendor/
/composer.phar
/phpstan.neon

# Runtime files.
/assets/*
!/assets/site/
!/assets/site/**
/assets/site/.htaccess
/assets/site/index.html
/core/custom/cache/
/core/custom/storage/
/core/custom/logs/
/core/custom/config/app/providers/
/core/custom/config/app/aliases/

# Local databases and secrets.
*.sqlite
*.sqlite3
/database.sqlite
.env
/core/custom/.env

# Local tooling.
/node_modules/
npm-debug.log
.idea/
.vscode/
*.swp
*.swo
.DS_Store
Thumbs.db
Desktop.ini
GITIGNORE;

    public function run(): void
    {
        $this->writeProjectGitignore();

        $homeTemplate = $this->ensureTemplate('home', 'Home', 1);
        $blogTemplate = $this->ensureTemplate('blog', 'Blog');
        $postTemplate = $this->ensureTemplate('post', 'Blog Post');
        $contactTemplate = $this->ensureTemplate('contact', 'Contact');

        $this->ensureHomeResource((int) $homeTemplate->id);

        $blog = $this->ensureResource(0, 'blog', [
            'pagetitle' => 'Blog',
            'longtitle' => 'Blog',
            'menutitle' => 'Blog',
            'description' => 'Latest notes and updates.',
            'introtext' => 'Latest notes and updates.',
            'content' => '<h1>Blog</h1><p>Latest notes, release updates, and short practical articles.</p>',
            'isfolder' => 1,
            'template' => (int) $blogTemplate->id,
            'menuindex' => 1,
            'hidemenu' => 0,
        ]);

        $this->seedPosts((int) $blog->id, (int) $postTemplate->id);

        $this->ensureResource(0, 'contacts', [
            'pagetitle' => 'Contacts',
            'longtitle' => 'Contacts',
            'menutitle' => 'Contacts',
            'description' => 'Send a message from the public site.',
            'introtext' => 'Send a message from the public site.',
            'content' => '<h1>Contacts</h1><p>Send a short message and the site will deliver it to the configured email sender.</p>',
            'isfolder' => 0,
            'template' => (int) $contactTemplate->id,
            'menuindex' => 2,
            'hidemenu' => 0,
        ]);
    }

    private function writeProjectGitignore(): void
    {
        $root = defined('EVO_BASE_PATH') ? EVO_BASE_PATH : dirname(__DIR__, 6);
        $path = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.gitignore';

        file_put_contents($path, self::PROJECT_GITIGNORE . PHP_EOL);
    }

    private function ensureTemplate(string $alias, string $name, ?int $id = null): SiteTemplate
    {
        $template = $id ? SiteTemplate::query()->find($id) : null;
        $template = $template ?: SiteTemplate::query()->where('templatealias', $alias)->first();
        $template = $template ?: new SiteTemplate();

        $now = time();
        if (!$template->exists || empty($template->createdon)) {
            $template->createdon = $now;
        }

        $template->templatename = $name;
        $template->templatealias = $alias;
        $template->description = $name . ' template';
        $template->editor_type = 0;
        $template->category = 0;
        $template->icon = '';
        $template->template_type = 0;
        $template->content = '[*content*]';
        $template->locked = 0;
        $template->selectable = 1;
        $template->editedon = $now;
        $template->save();

        return $template;
    }

    private function ensureHomeResource(int $templateId): SiteContent
    {
        $siteStart = (int) EvolutionCMS()->getConfig('site_start', 1);
        $resource = $siteStart > 0 ? SiteContent::query()->find($siteStart) : null;
        $resource = $resource ?: SiteContent::query()->where('parent', 0)->orderBy('id')->first();
        $resource = $resource ?: new SiteContent();

        $attributes = [
            'pagetitle' => 'Home',
            'longtitle' => 'Home',
            'menutitle' => 'Home',
            'description' => 'A minimal blog starter for Evolution CMS.',
            'introtext' => 'A minimal blog starter for Evolution CMS.',
            'alias' => 'home',
            'parent' => 0,
            'isfolder' => 0,
            'template' => $templateId,
            'menuindex' => 0,
            'hidemenu' => 0,
        ];

        if (!$resource->exists || $this->shouldReplaceHomeContent((string) $resource->content)) {
            $attributes['content'] = '<h1>Home</h1><p>This is the main page content. Edit it in Manager and keep the latest blog posts visible below.</p>';
        }

        $this->applyResourceAttributes($resource, $attributes);

        return $resource;
    }

    private function ensureResource(int $parent, string $alias, array $attributes): SiteContent
    {
        $resource = SiteContent::query()
            ->where('parent', $parent)
            ->where('alias', $alias)
            ->first() ?: new SiteContent();

        $attributes['parent'] = $parent;
        $attributes['alias'] = $alias;

        $this->applyResourceAttributes($resource, $attributes);

        return $resource;
    }

    private function applyResourceAttributes(SiteContent $resource, array $attributes): void
    {
        $now = time();
        $defaults = [
            'type' => 'document',
            'contentType' => 'text/html',
            'link_attributes' => '',
            'published' => 1,
            'pub_date' => 0,
            'unpub_date' => 0,
            'introtext' => '',
            'richtext' => 1,
            'searchable' => 1,
            'cacheable' => 1,
            'createdby' => 1,
            'editedby' => 1,
            'deleted' => 0,
            'deletedon' => 0,
            'deletedby' => 0,
            'publishedby' => 1,
            'hide_from_tree' => 0,
            'privateweb' => 0,
            'privatemgr' => 0,
            'content_dispo' => 0,
            'alias_visible' => 1,
        ];

        $values = array_merge($defaults, $attributes);

        if (!$resource->exists || empty($resource->createdon)) {
            $values['createdon'] = $values['createdon'] ?? $now;
        }

        $values['editedon'] = $now;
        $values['publishedon'] = $values['publishedon'] ?? $now;

        foreach ($values as $key => $value) {
            $resource->setAttribute($key, $value);
        }

        $resource->save();
    }

    private function shouldReplaceHomeContent(string $content): bool
    {
        $content = trim($content);

        return $content === ''
            || str_contains($content, 'Install Successful')
            || str_contains($content, 'Evolution CMS Community')
            || str_contains($content, 'This is the main page content.');
    }

    private function seedPosts(int $blogId, int $templateId): void
    {
        $now = time();
        $posts = [
            [
                'alias' => 'welcome-to-the-blog',
                'title' => 'Welcome to the blog',
                'summary' => 'A short starting point for a clean Evolution CMS blog.',
                'body' => '<p>This first post keeps the starter content small and useful. Replace it with your own launch note when the project goes live.</p><p>The preset keeps templates, views, controllers, and seeds close together so the project stays easy to understand.</p>',
            ],
            [
                'alias' => 'writing-with-evolution-cms',
                'title' => 'Writing with Evolution CMS',
                'summary' => 'Simple publishing works best when the resource tree stays readable.',
                'body' => '<p>Use the Blog page as the parent for posts and keep the public navigation focused on top-level sections.</p><p>Editors can manage content in Manager while the preset controls the front-end layout.</p>',
            ],
            [
                'alias' => 'designing-theme-options',
                'title' => 'Designing theme options',
                'summary' => 'DaisyUI themes make it easy to start with light and dark modes.',
                'body' => '<p>The theme config can expose the full DaisyUI catalog or only the themes you want for a client project.</p><p>Custom themes live next to the preset styles and can change the look without changing the page structure.</p>',
            ],
            [
                'alias' => 'keeping-presets-small',
                'title' => 'Keeping presets small',
                'summary' => 'A good preset should be easy to read before it becomes powerful.',
                'body' => '<p>The blog preset starts with only the pages, templates, and controllers needed for publishing.</p><p>More complex features can be added later as focused extras or project-specific code.</p>',
            ],
            [
                'alias' => 'preparing-contact-workflows',
                'title' => 'Preparing contact workflows',
                'summary' => 'The contact page logs every request and sends it to the configured site email.',
                'body' => '<p>Contact submissions are stored in the system event log so a request is still visible even if mail delivery is not configured locally.</p><p>The recipient is taken from the standard emailsender setting.</p>',
            ],
        ];

        foreach ($posts as $index => $post) {
            $publishedAt = $now - ($index * 86400);

            $this->ensureResource($blogId, $post['alias'], [
                'pagetitle' => $post['title'],
                'longtitle' => $post['title'],
                'menutitle' => $post['title'],
                'description' => $post['summary'],
                'introtext' => $post['summary'],
                'content' => $post['body'],
                'isfolder' => 0,
                'template' => $templateId,
                'menuindex' => $index,
                'hidemenu' => 1,
                'publishedon' => $publishedAt,
                'createdon' => $publishedAt,
            ]);
        }
    }
}
