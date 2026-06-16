# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Plugin overview

**Organization and Contacts Manager** (`organization-contacts-manager`) — a WordPress admin-only plugin for managing organizations, contacts, and groups, and sending press release emails to them.

- **Plugin entry point:** `main.php` — bootstraps everything via `require_once` calls; no autoloader.
- **Admin menu slug:** `media_manager` (despite the plugin name). Subpages: `press_release`, `organization_page`, `contact_page`, `group_page`, `settings_page`.
- **No build system.** Edit PHP, JS, and CSS files directly — there is no npm, Composer, or compilation step.

## Database tables (created on activation)

All tables use the `$wpdb->prefix` prefix. Defined in `includes/activation-handler.php`:

| Table | Purpose |
|---|---|
| `organizations` | Orgs; FK to `groups` |
| `contacts` | Contacts; FK to `organizations` (CASCADE delete) |
| `groups` | Contact/org groups |
| `group_taxonomy` | Tag types for groups; FK to `groups` |
| `group_relationships` | Pivot: object ↔ group_taxonomy |
| `general_options` | Which post type/category holds press releases |
| `sender_profiles` | Email sender identity + styling; FKs to `section_options` |
| `section_options` | Reusable HTML blocks (header/about/footer) for emails |

Schema changes require manually running `dbDelta()` — re-activating the plugin runs `plugin_activation()` again.

## Architecture patterns

**CRUD modules** follow a consistent split across files under `includes/{entity}/`:
- `*-display.php` — renders the admin list/table page
- `*-add-form.php` — renders the add form
- `*-save-handler.php` — processes the POST and inserts to DB
- `*-edit-form.php` / `*-edit-handler.php` — edit flow
- `*-delete-handler.php` — deletes the record

**AJAX** — all async operations use `wp_ajax_{action}` hooks. The handler function calls `wp_send_json_success()` / `wp_send_json_error()` and `wp_die()`. Nonce verification (`wp_verify_nonce`) and `current_user_can('manage_options')` checks are required on every AJAX handler.

**Email send flow:**
1. Press Release page → modal (`includes/press-release/press-release-modal.php`) collects recipient selection and profile
2. JS posts to `wp_ajax_send_to_sel_contacts` → `includes/email/send-to-contacts.php`
3. Handler `require`s `includes/email/email-content.php` inline (not a function call — it runs in the handler's scope and sets `$emailBody` using `$_POST['profile_id']`, `$_POST['post_id']`, etc.)
4. `wp_mail()` sends with `wp_mail_from` / `wp_mail_from_name` filters applied temporarily

**Email content (`email-content.php`)** builds `$emailBody` by pulling the WP post content and applying inline CSS (font, color, link color) from the selected sender profile. Featured image placement is controlled by `featured_img_pos` on the profile (`display-top`, `display-below-headline`, `email-attach`, `ignore`).

## Frontend dependencies (CDN, enqueued in `vendor/vendor.php`)

- **DataTables 1.13.5** — sortable/searchable admin tables
- **Select2 4.1.0-rc.0** — enhanced dropdowns
- **SweetAlert2 11** — confirmation dialogs
- **HugeRTE** (TinyMCE fork) — rich text editor for section content (header/about/footer blocks)

Assets in `assets/js/` and `assets/css/` are enqueued via `includes/admin-style-script.php`.

## Settings page structure

`includes/settings-display.php` handles both display and AJAX handlers for the Settings page. Two active tabs:
- **General Settings** — selects which post type + categories contain press releases (saved to `general_options`)
- **Sender Profiles** — create/edit/delete profiles controlling sender name, email, fonts, colors, and featured image behavior

Section content (header/about/footer HTML blocks) exists in the DB and settings UI but the corresponding tabs are currently commented out in the nav.

## Security conventions

- Always use `$wpdb->prepare()` for DB queries with user-supplied values
- Use `sanitize_text_field()`, `sanitize_email()`, `sanitize_hex_color()`, `wp_kses_post()` at input boundaries
- All AJAX handlers must verify nonce and `current_user_can('manage_options')`
- Use `esc_html()`, `esc_attr()`, `esc_url()` on output
