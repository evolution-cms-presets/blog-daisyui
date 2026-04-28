<?php

namespace EvolutionCMS\BlogDaisyui\Seeders;

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

        $template = SiteTemplate::query()->find(1);

        if (!$template) {
            return;
        }

        $template->templatealias = 'home';
        $template->editedon = time();
        $template->save();
    }

    private function writeProjectGitignore(): void
    {
        $root = defined('EVO_BASE_PATH') ? EVO_BASE_PATH : dirname(__DIR__, 6);
        $path = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.gitignore';

        file_put_contents($path, self::PROJECT_GITIGNORE . PHP_EOL);
    }
}
