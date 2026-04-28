# Evolution CMS Blog DaisyUI Preset

Minimal blog-oriented DaisyUI project-layer preset for Evolution CMS 3.5.x. It starts from the default DaisyUI layout and declares the required blog editing Extra for the installer.

## What This Preset Contains

```text
core/custom/
  .gitignore
  composer.json
  config/
    cms/settings/ControllerNamespace.php
    presets/blog-daisyui.php
  packages/blog-daisyui/src/
    composer.json
    BlogDaisyuiServiceProvider.php
    Controllers/
      BaseController.php
      HomeController.php
    Seeders/
      HomeTemplateSeeder.php
views/
  home.blade.php
  layouts/base.blade.php
  partials/header.blade.php
themes/blog-daisyui/
  css/app.css
  css/themes.css
  js/theme.js
.gitignore
```

Evolution core, manager files, runtime cache, database files, and local secrets stay outside this repository. DaisyUI is loaded from the official CDN package with the full themes bundle, so the preset works immediately after install without a Node build step.

## Required Extras

This preset declares required Composer Extras in `core/custom/composer.json`:

```json
{
  "require": {
    "evolution-cms/etinymce": "*"
  }
}
```

Installer versions that support Composer-backed preset Extras will install `eTinyMCE` automatically and still run the Extras install flow so assets, provider config, migrations, and cache clearing are handled. In TUI mode it is selected and locked; optional Extras can still be added or skipped.

Additional SEO tooling can be added after the upstream local-redirect fix is released.

## Theme Configuration

Front theme controls are configured in `core/custom/config/presets/blog-daisyui.php`. The file is copied into `core/custom/config` with the preset, matching the same custom-config convention used by installable Extras such as `core/custom/config/seiger/settings/sGallery.php` and `core/custom/config/seiger/settings/sCommerce.php`.

```php
'theme' => [
    'enabled' => true, // Enables frontend theme handling at all.
    'show_toggle' => true, // Shows the sun/moon light-dark switch.
    'show_themes' => true, // Shows the themes menu with the lists below.
    'default_light' => 'evolight', // First light theme used by default and by the toggle.
    'default_dark' => 'evodark', // First dark theme used by default and by the toggle.
    'light' => [
        'evolight' => 'EVO Light',
        'evolightness' => 'EVO Lightness',
    ],
    'dark' => [
        'evodark' => 'EVO Dark',
        'evodarkness' => 'EVO Darkness',
    ],
],
```

The default preset ships four custom frontend DaisyUI themes in `themes/blog-daisyui/css/themes.css`: `evolight`, `evolightness`, `evodark`, and `evodarkness`. To add your own custom theme, add a `[data-theme="my-theme"]` block to `themes.css`, then add it to the `light` or `dark` list in the config. Built-in DaisyUI themes can also be exposed by adding their names to the same lists; the full DaisyUI themes bundle is already loaded.

By default the config exposes the full DaisyUI theme catalog plus the four custom EVO themes. To leave only the classic pair, keep just:

```php
'default_light' => 'light',
'default_dark' => 'dark',
'light' => ['light' => 'Light'],
'dark' => ['dark' => 'Dark'],
```

You can also keep the full lists and change only `default_light` / `default_dark` to choose which light and dark theme the toggle uses first.

## Install Through Evo Installer

The preset should be installed by the Evolution CMS installer, not applied manually as a second step. The installer creates the target project first, then copies this preset as the project-layer bootstrap and installs the required Extras.

### TUI Install

Use TUI mode when you want the installer to ask for database, admin user, language, and optional Extras. This is the shortest Blog DaisyUI install:

```bash
evo install /path/to/my-site \
  --branch=3.5.x \
  --preset=evolution-cms-presets/blog-daisyui
```

For a local preset checkout during preset development:

```bash
evo install /tmp/blog-daisyui-preset-check \
  --branch=3.5.x \
  --preset=/path/to/blog-daisyui-preset
```

Skip optional Extras on the Extras selection screen when you want only the required blog baseline.

### CLI Install

Use CLI mode when you want a fully non-interactive install:

```bash
evo install /path/to/my-site \
  --cli \
  --branch=3.5.x \
  --db-type=sqlite \
  --db-name=database.sqlite \
  --admin-username=admin \
  --admin-email=admin@example.com \
  --admin-password=change-me \
  --admin-directory=manager \
  --language=uk \
  --preset=evolution-cms-presets/blog-daisyui
```

For local installer development from an installer checkout:

```bash
cd /path/to/installer
go run ./cmd/evo install /path/to/my-site \
  --cli \
  --branch=3.5.x \
  --db-type=sqlite \
  --db-name=database.sqlite \
  --admin-username=admin \
  --admin-email=admin@example.com \
  --admin-password=change-me \
  --admin-directory=manager \
  --language=uk \
  --preset=evolution-cms-presets/blog-daisyui
```

`evolution-cms-presets/blog-daisyui` is the preset source. The target project can still be committed and pushed as its own repository.

For local preset development, point the installer at the preset checkout:

```bash
go run ./cmd/evo install /tmp/blog-daisyui-preset-check \
  --cli \
  --branch=3.5.x \
  --db-type=sqlite \
  --db-name=database.sqlite \
  --admin-username=admin \
  --admin-email=admin@example.com \
  --admin-password=change-me \
  --admin-directory=manager \
  --language=uk \
  --preset=/path/to/blog-daisyui-preset
```

Use `--preset=evolution-cms-presets/blog-daisyui@dev` when you want the installer to pull a development branch or tag from GitHub.

After install, the generated project `.gitignore` keeps Evolution core, manager, runtime cache, local SQLite databases, and IDE files out of the site repository. A fresh `git status` should show only the project layer: `core/custom`, `views`, `themes/blog-daisyui`, `robots.txt`, and `.gitignore`.

## Development Contract

- Put PHP site logic in `core/custom/packages/blog-daisyui/src/`.
- Put required Composer package dependencies in `core/custom/composer.json`.
- Put frontend templates in `views/`.
- Put theme assets in `themes/blog-daisyui/`.
- DaisyUI and Tailwind browser assets are loaded in `views/layouts/base.blade.php`.
- Configure available frontend themes, default light/dark themes, and themes menu visibility in `core/custom/config/presets/blog-daisyui.php`.
- Put custom frontend DaisyUI theme tokens in `themes/blog-daisyui/css/themes.css`.
- Light/dark switching and grouped DaisyUI themes menu persistence live in `themes/blog-daisyui/js/theme.js`.
- Keep the preset minimal; project-specific content belongs in the site repo that consumes it.
- `HomeTemplateSeeder` assigns the default site template alias to `home`, so Evolution can resolve `views/home.blade.php`.

## Next Site Step

Use this preset as the DaisyUI blog base, then create project-specific branches or repositories that replace starter naming, add real content views, and grow controllers only when the site needs them.
