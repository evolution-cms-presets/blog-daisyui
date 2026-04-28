(function () {
  var config = window.EvoBlogDaisyuiTheme || {};

  if (config.enabled === false) {
    return;
  }

  var storageKey = config.storageKey || 'evo.blogDaisyui.theme';
  var storageLight = storageKey + '.light';
  var storageDark = storageKey + '.dark';
  var root = document.documentElement;
  var swap = document.getElementById('theme-swap');
  var menu = document.getElementById('theme-menu');

  if (!swap && !menu) {
    return;
  }

  function themeNames(group) {
    return (group || []).map(function (item) {
      if (typeof item === 'string') {
        return item;
      }

      return item && item.name;
    }).filter(Boolean);
  }

  var lightThemes = themeNames(config.light);
  var darkThemes = themeNames(config.dark);
  var defaultLight = config.defaultLight || lightThemes[0] || 'light';
  var defaultDark = config.defaultDark || darkThemes[0] || defaultLight;

  var lightSet = new Set(lightThemes);
  var darkSet = new Set(darkThemes);
  var allowedSet = new Set(lightThemes.concat(darkThemes));

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

  function isAllowedTheme(theme) {
    return allowedSet.size === 0 || allowedSet.has(theme);
  }

  function setActiveItem(theme) {
    if (!menu) {
      return;
    }

    menu.querySelectorAll('[data-theme-item]').forEach(function (item) {
      item.classList.toggle('active', item.dataset.themeItem === theme);
      item.classList.toggle('bg-base-content/10', item.dataset.themeItem === theme);
    });
  }

  function setVisibleThemeGroup(isDark) {
    if (!menu) {
      return;
    }

    if (!swap) {
      menu.querySelectorAll('[data-theme-group]').forEach(function (item) {
        item.classList.remove('hidden');
      });
      return;
    }

    menu.querySelectorAll('[data-theme-group="light"]').forEach(function (item) {
      item.classList.toggle('hidden', isDark);
    });
    menu.querySelectorAll('[data-theme-group="dark"]').forEach(function (item) {
      item.classList.toggle('hidden', !isDark);
    });
  }

  function setTheme(theme) {
    if (!theme || !isAllowedTheme(theme)) {
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
    if (swap) {
      swap.checked = isDark;
      swap.value = readStorage(storageDark) || defaultDark;
    }

    setVisibleThemeGroup(isDark);
    setActiveItem(theme);
  }

  function initialTheme() {
    var saved = readStorage(storageKey);
    if (saved && isAllowedTheme(saved)) {
      return saved;
    }

    var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (prefersDark) {
      var savedDark = readStorage(storageDark);
      return savedDark && isAllowedTheme(savedDark) ? savedDark : defaultDark;
    }

    var savedLight = readStorage(storageLight);
    return savedLight && isAllowedTheme(savedLight) ? savedLight : defaultLight;
  }

  setTheme(initialTheme());

  if (swap) {
    swap.addEventListener('change', function () {
      if (swap.checked) {
        var savedDark = readStorage(storageDark);
        setTheme(savedDark && isAllowedTheme(savedDark) ? savedDark : defaultDark);
        return;
      }

      var savedLight = readStorage(storageLight);
      setTheme(savedLight && isAllowedTheme(savedLight) ? savedLight : defaultLight);
    });
  }

  if (menu) {
    menu.addEventListener('click', function (event) {
      var item = event.target.closest('[data-theme-item]');
      if (!item) {
        return;
      }

      setTheme(item.dataset.themeItem);
    });
  }
})();
