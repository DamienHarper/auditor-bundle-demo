# auditor-bundle Demo

A demo application showcasing **[damienharper/auditor-bundle](https://github.com/DamienHarper/auditor-bundle) v7** with Symfony 8.

## Stack

- PHP 8.4+ / Symfony 8.x
- Doctrine ORM 3.2+ / DBAL 4.0+
- auditor-bundle ^7.0
- SQLite (single file)
- Tailwind CSS 4 via CDN

## Quick Start

```bash
composer install
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console audit:schema:update --force
php bin/console doctrine:fixtures:load --no-interaction
symfony server:start
```

## URLs

| URL | Description |
|-----|-------------|
| `/` | Blog homepage — published posts |
| `/posts` | All posts with CRUD |
| `/authors` | Authors with CRUD + audit preview |
| `/tags` | Tags with CRUD |
| `/login` | Login (`admin@blog.com` / `password`) |
| `/history` | **Custom audit journal** (filtered, paginated) |
| `/audit` | **Native auditor-bundle viewer** |
| `/audit/App-Entity-Post/{id}` | Entity-specific audit stream |
| `/audit/transaction/{hash}` | Transaction audit stream |

## Fixture Lots

The fixtures demonstrate 6 distinct transaction batches, each generating different audit events:

| Lot | Content | Audit Events |
|-----|---------|--------------|
| **1** | Users, Authors, Tags, Posts (draft, no author) | `insert ×13` |
| **2** | Assign authors to posts | `update ×3`, `associate ×4` |
| **3** | Assign ManyToMany tags to posts | `associate ×7` |
| **4** | Add 9 comments, publish 2 posts | `insert ×9`, `update ×2` |
| **5** | Update author bio, post title/excerpt, add co-author | `update ×3` |
| **6** | Dissociate a tag, change author, delete a comment | `dissociate ×1`, `update ×1`, `remove ×1` |

## Audited Entities

- `Author` — name, email, bio
- `Post` — title, body, excerpt, status, author (ManyToOne), coauthor (ManyToOne), tags (ManyToMany)
- `Comment` — body, authorName, post (ManyToOne)
- `Tag` — name, color

`created_at` and `updated_at` fields are ignored from auditing via `#[Audit\Ignore]`.

`User` is **not audited** (no `#[Auditable]` attribute).
