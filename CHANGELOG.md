## Elgg 6.x Migration (2026-05-09)

- Bumped `elgg/elgg` to `~6.1.0`, `php` to `>=8.1`, added `ext-intl`
- No JS files — no AMD→ESM conversion needed
- No deprecated PHP hook functions
- Added docker/elgg6/ test stack
- No data migration needed

<a name="5.0.0"></a>
## [5.0.0] (2026-05-08)

### Migration: Elgg 4.x → 5.x

* Unified events model: `'hooks'` key renamed to `'events'` in `elgg-plugin.php`
* Handler signatures updated: `Elgg\Hook` → `Elgg\Event` in all handler classes
* `get_current_language()` replaced with `elgg_get_current_language()`
* PHP requirement bumped to `>=8.2`, Elgg requirement to `^5.0`

<a name="4.0.0"></a>
## [4.0.0] (2026-04-17)

### Migration: Elgg 3.x → 4.x

* Ported to Elgg 4.x Bootstrap architecture — dropped `start.php`, wired via `elgg-plugin.php` and invokable hook handler classes
* Dropped `manifest.xml` — plugin metadata moved to `composer.json` (`elgg/elgg ^4.0`)
* All hook callbacks converted to `__invoke(Hook $hook)` invokable classes
* `unserialize()` hardened with `['allowed_classes' => false]`
* Plugin ID lowercased to `prototyper_profile` in all callsites

<a name="1.0.2"></a>
## [1.0.2](https://github.com/hypeJunction/Elgg-prototyper_profile/compare/1.0.0...v1.0.2) (2016-02-25)


### Bug Fixes

* **forms:** pass correct entity to the prototype ([b94ad2b](https://github.com/hypeJunction/Elgg-prototyper_profile/commit/b94ad2b))
* **views:** do not needlessly overwrite profile edit resource view ([7abfa47](https://github.com/hypeJunction/Elgg-prototyper_profile/commit/7abfa47))



<a name="1.0.1"></a>
## [1.0.1](https://github.com/hypeJunction/Elgg-prototyper_profile/compare/1.0.0...v1.0.1) (2016-02-24)


### Bug Fixes

* **views:** do not needlessly overwrite profile edit resource view ([7abfa47](https://github.com/hypeJunction/Elgg-prototyper_profile/commit/7abfa47))



<a name="1.0.0"></a>
# 1.0.0 (2015-11-08)


### Bug Fixes

* **docs:** fix typo in screenshot URL ([3b471e0](https://github.com/hypeJunction/Elgg-prototyper_profile/commit/3b471e0))
* **grunt:** use correct branch name ([5ffe3b7](https://github.com/hypeJunction/Elgg-prototyper_profile/commit/5ffe3b7))




