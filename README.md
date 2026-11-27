# auditor-bundle Demo

A demo application showcasing **[damienharper/auditor-bundle](https://github.com/DamienHarper/auditor-bundle) v8** with Symfony 8.1, running the 5.0 / 2.0 / 8.0 releases of the auditor family.

## Stack

- PHP 8.4+ (with `ext-intl`) / Symfony 8.1
- Doctrine ORM 3.8+ / DBAL 4.6+
- auditor `^5.0`, auditor-doctrine-provider `^2.0`, auditor-bundle `^8.0`
- `symfony/ux-twig-component` for the `<twig:DhAuditor:History>` component
- SQLite (single file)

## Quick Start

```bash
composer install
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console audit:schema:update --force
php bin/console app:fixtures:load --no-interaction
symfony server:start
```

The audit tables (and the `audit_root` index of the audit roots) are managed by `audit:schema:update`, not by the
Doctrine migrations.

### Upgrading an existing demo database

From audit schema v1 (auditor-bundle 7.x):

```bash
php bin/console audit:schema:migrate --force --convert-all
php bin/console audit:schema:update --force
php bin/console audit:roots:rebuild
```

From an earlier 8.x-dev demo, `audit:schema:update --force` adds the `audit_root` table; reload the fixtures (or run
`audit:roots:rebuild`) to index the existing entries under their roots.

## What the demo shows

| Feature | Where |
|---|---|
| **Audit roots** — `#[AuditRoot('author')]` on `Post`, `#[AuditRoot('post')]` and `#[AuditRoot('post.author')]` on `Comment` | The history of a post includes its comments; the history of an author includes their posts and the comments on them |
| **`<twig:DhAuditor:History>`** — the history of an object in the pages of the application, told as phrases | `/posts/{id}`, `/authors/{id}` |
| **Last change of a field** — `dh_auditor_last_changes()`, `dh_auditor_field_label()` | "Last changes" card of `/posts/{id}` and `/authors/{id}` |
| **Phrases and labels** — `translations/dh_auditor_fields+intl-icu.en.yaml`, `translations/dh_auditor_history+intl-icu.en.yaml` | "moved “…” from Published to Archived", "tagged “…” with php and symfony", "Summary" for `excerpt` |
| **Enum values** — `Post::$status` is a `PostStatus` enum implementing `TranslatableInterface` | Its label in the phrases ("Draft → Published") and the "status" icon |
| **`#[DiffLabel]`** — `App\Audit\TagColorLabelResolver` on `Tag::$color` | "changed Colour: Violet → Purple" instead of hex codes |
| **`entity_label_resolver`** — `App\Audit\EntityLabelResolver` | A comment is labelled "Jane Doe’s comment" in the diffs |
| **Blame context** — `BlameContextStack::run(new BlameContext('import', …))` in the fixtures; the automatic `console` context of commands | "via import · Import batch #1" / "via console · app:fixtures:load" badges |
| **Snapshot** — tags created with auditing disabled, then `Snapshotter` (what `audit:snapshot` runs) | "recorded the initial state of 15 tags" in `/audit/App-Entity-Tag` |
| **Custom journal** — `HistoryGrouper::byTransaction()` + the `HistoryRows` of the viewer | `/history`: one line per transaction across the four audit tables |
| **Read access** — `roles: { view: [ROLE_USER] }` on every entity in `dh_auditor.yaml`, `dh_auditor_is_viewable()` in the templates | The post and author pages are public; their history and last changes are shown to signed-in users only |

## URLs

| URL | Description |
|-----|-------------|
| `/` | Blog homepage — published posts |
| `/posts` | All posts with CRUD |
| `/posts/{id}` | Post, its history (post + comments) and the last change of its fields |
| `/authors` | Authors with CRUD |
| `/authors/{id}` | Author, their history (author + posts + comments) and the last change of their fields |
| `/tags` | Tags with CRUD |
| `/login` | Login (`admin@blog.com` / `password`) |
| `/history` | **Custom audit journal**: one action per transaction, told as a phrase (login required) |
| `/audit` | **Native auditor-bundle viewer** |
| `/audit/App-Entity-Post/{id}` | History of a post in the viewer |
| `/audit/transaction/{id}` | One action (ULID transaction) |
| `/audit/user/{id}` | Per-user audit timeline |
| `/audit/App-Entity-Post/{id}/revisions` | Pick two revisions to compare |
| `/audit/export?format=csv\|json\|ndjson` | Audit export (`entity`, `id`, `blame_id`, `from`, `to`, `anonymize`) |

## Fixture Lots

`app:fixtures:load` loads 16 lots, each in its own flush, so each gets its own transaction (and action) in the audit:

| Lot | Content | Audit Events |
|-----|---------|--------------|
| **0** | 15 tags created with auditing disabled, then snapshotted | `insert ×15` flagged as snapshot |
| **1** | 3 users (not audited), 15 authors | `insert ×15` |
| **2–3** | 60 draft posts | `insert ×60` |
| **4** | Main author on every post | `update`, `associate` |
| **5–6** | 2–4 tags per post | `associate` |
| **7–8** | ~200 comments | `insert` |
| **9** | ~100 comments imported in an `import` blame context | `insert`, `via: import` |
| **10** | 40 posts published, with a summary | `update ×40` |
| **11** | Author bios | `update ×15` |
| **12** | Co-authors, body revisions | `update`, `associate` |
| **13** | Title revisions | `update ×15` |
| **14** | Tags removed, comments deleted | `dissociate`, `remove` |
| **15** | 5 posts archived, a tag colour changed | `update ×6` |

## Audited Entities

- `Author` — name, email, bio
- `Post` — title, body, excerpt, status (`PostStatus` enum), author (ManyToOne), coauthor (ManyToOne), tags (ManyToMany); root: `author`
- `Comment` — body, authorName, post (ManyToOne); roots: `post`, `post.author`
- `Tag` — name, color (`#[DiffLabel]`)

`created_at` and `updated_at` fields are ignored from auditing via `#[Audit\Ignore]`.

`User` is **not audited** (no `#[Auditable]` attribute).

## Styling note

The History component and the rows of `/history` are styled with the Tailwind classes of the viewer: the pages that
show them load `bundles/dhauditor/app.css`, the demo stylesheet declares the Tailwind layer order and keeps its own
utility classes in the `utilities` layer, and the theme switch also toggles the `dark` class the bundle templates
follow.
