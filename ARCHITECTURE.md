# prototyper_profile — Architecture (Elgg 6.x)

## Summary

Integrates hypePrototyper with Elgg user profiles. Provides:
- A prototyper-backed profile edit form replacing the stock `profile/edit` action
- Admin UI to configure the prototype (field layout) per role
- A hook to inject configured prototype fields into the prototyper field resolution pipeline

This is a thin integration plugin — it contains no entity types or subtypes of its own.

## Directory Structure

```
prototyper_profile/
├── actions/
│   ├── profile/edit.php          # Replaces stock profile save via hypePrototyper action service
│   └── profile/prototype.php     # Admin action — serializes prototype fields to plugin settings
├── classes/hypeJunction/PrototyperProfile/
│   ├── Bootstrap.php             # Elgg 5.x PluginBootstrap (no-op methods, wiring via elgg-plugin.php)
│   ├── FilterFormVars.php        # Event: view_vars / input/form — enables validation on profile/edit form
│   ├── GetConfigFields.php       # Event: profile:fields / profile — exposes prototype fields as profile config
│   └── GetPrototypeFields.php    # Event: prototype / profile/edit — resolves saved prototype or builds from profile_fields config
├── views/default/
│   ├── admin/appearance/
│   │   ├── profile_fields.php    # Admin appearance page — renders prototype editor form
│   │   └── profile_fields/filter.php  # Role filter dropdown (requires roles plugin)
│   ├── forms/profile/edit.php    # Replaced profile edit form (renders prototyper form)
│   └── profile/details.php      # Profile details view (banned state + aboutme)
├── languages/                    # i18n strings
├── elgg-plugin.php               # Plugin manifest — events, actions, bootstrap, deps
└── composer.json                 # Elgg 5.x metadata (elgg/elgg ^5.0)
```

## Registered Events

| Event | Type | Handler | Priority |
|-------|------|---------|---------|
| `prototype` | `profile/edit` | `GetPrototypeFields` | default |
| `profile:fields` | `profile` | `GetConfigFields` | default |
| `view_vars` | `input/form` | `FilterFormVars` | 200 |

## Actions

| Route | Access | Purpose |
|-------|--------|---------|
| `profile/edit` | logged-in | Save profile via hypePrototyper action service |
| `profile/prototype` | admin | Serialize and store field prototype per role |

## Dependencies

| Plugin | Constraint |
|--------|-----------|
| `hypeprototyper` | `must_be_active: true` |

hypePrototyper in turn requires `hypeapps` and `hypelists`.

## Data Storage

Prototype field layouts are stored as serialized arrays in plugin settings with keys `prototype:<role_name>` (e.g. `prototype:default`, `prototype:member`). Unserialized with `allowed_classes => false` for safety.

## Migration Notes (4.x → 5.x)

- `'hooks'` key in `elgg-plugin.php` renamed to `'events'` (Elgg 5.x unified events model)
- All handler signatures updated: `use Elgg\Hook` → `use Elgg\Event`, `Hook $hook` → `Event $event`
- `get_current_language()` replaced with `elgg_get_current_language()` (removed in Elgg 5.x)
- `composer.json` bumped: `php >=8.2`, `elgg/elgg ^5.0`
- Docker stack updated: `php:8.2-apache`, `mysql:8.0`, `elgg/elgg 5.1.12`
- No data migration script needed — plugin settings format unchanged

## Migration Notes (3.x → 4.x)

- Dropped `start.php` — all wiring moved to `elgg-plugin.php` (declarative hooks) and `Bootstrap`
- Dropped `manifest.xml` — metadata now in `composer.json` (`elgg/elgg ^4.0`)
- All hook callbacks converted to invokable classes implementing `__invoke(Hook $hook)`
- `unserialize()` calls hardened with `['allowed_classes' => false]`
- Plugin ID lowercased to `prototyper_profile` in all `elgg_get_plugin_from_id()` callsites
- CSS registration: no CSS registered by this plugin; styles inherited from hypePrototyper
- No data migration script needed — plugin settings format unchanged (serialize → serialize)
