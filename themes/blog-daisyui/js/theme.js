(function () {
  var storageKey = 'evo.blogDaisyui.theme';
  var storageLight = 'evo.blogDaisyui.theme.light';
  var storageDark = 'evo.blogDaisyui.theme.dark';
  var root = document.documentElement;
  var swap = document.getElementById('theme-swap');
  var menu = document.getElementById('theme-menu');

  if (!swap || !menu) {
    return;
  }

  var lightThemes = [
    'acid',
    'autumn',
    'bumblebee',
    'caramellatte',
    'cmyk',
    'corporate',
    'cupcake',
    'cyberpunk',
    'emerald',
    'fantasy',
    'garden',
    'lemonade',
    'light',
    'lofi',
    'nord',
    'pastel',
    'retro',
    'silk',
    'valentine',
    'winter',
    'wireframe',
  ];

  var darkThemes = [
    'abyss',
    'aqua',
    'black',
    'business',
    'coffee',
    'dark',
    'dim',
    'dracula',
    'forest',
    'halloween',
    'luxury',
    'night',
    'sunset',
    'synthwave',
  ];

  var lightSet = new Set(lightThemes);
  var darkSet = new Set(darkThemes);

  function readStorage(key) {
    try {
      return window.localStorage.getItem(key);
    } catch (error) {
      return null;
    }
  }

  function writeStorage(key, value) {
    try {
      window.localStorage.setItem(key, value);
    } catch (error) {
      // Some browsers block localStorage in private contexts.
    }
  }

  function setActiveItem(theme) {
    menu.querySelectorAll('[data-theme-item]').forEach(function (item) {
      item.classList.toggle('active', item.dataset.themeItem === theme);
      item.classList.toggle('bg-base-content/10', item.dataset.themeItem === theme);
    });
  }

  function setVisibleThemeGroup(isDark) {
    menu.querySelectorAll('[data-theme-group="light"]').forEach(function (item) {
      item.classList.toggle('hidden', isDark);
    });
    menu.querySelectorAll('[data-theme-group="dark"]').forEach(function (item) {
      item.classList.toggle('hidden', !isDark);
    });
  }

  function setTheme(theme) {
    if (!theme) {
      return;
    }

    root.setAttribute('data-theme', theme);
    writeStorage(storageKey, theme);

    if (darkSet.has(theme)) {
      writeStorage(storageDark, theme);
    } else if (lightSet.has(theme)) {
      writeStorage(storageLight, theme);
    }

    var isDark = darkSet.has(theme);
    swap.checked = isDark;
    swap.value = readStorage(storageDark) || 'dark';

    setVisibleThemeGroup(isDark);
    setActiveItem(theme);
  }

  function initialTheme() {
    var saved = readStorage(storageKey);
    if (saved) {
      return saved;
    }

    var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (prefersDark) {
      return readStorage(storageDark) || 'dark';
    }

    return readStorage(storageLight) || 'light';
  }

  setTheme(initialTheme());

  swap.addEventListener('change', function () {
    if (swap.checked) {
      setTheme(readStorage(storageDark) || 'dark');
      return;
    }

    setTheme(readStorage(storageLight) || 'light');
  });

  menu.addEventListener('click', function (event) {
    var item = event.target.closest('[data-theme-item]');
    if (!item) {
      return;
    }

    setTheme(item.dataset.themeItem);
  });
})();
