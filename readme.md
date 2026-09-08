[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hparadiz/technexus/badges/quality-score.png?b=release)](https://scrutinizer-ci.com/g/hparadiz/technexus/?branch=release)

# About
A blogging content management system
- Uses TinyMCE Editor
- Pasted images into TinyMCE are automatically uploaded, thumbnailed, and embeded.
- Robust admin lets you create and edit posts.
- Fast, clean, tagging system with proper SEO optimized permalinks.
- Mark posts as drafts or published.
- Full access to html & css.

## Development

Install the locked Divergence 3.3 release with `composer install`. Configure the
database in `config/db.php`, then serve the public directory:

```sh
php -S 127.0.0.1:8000 -t public
```

To work against a sibling `../framework` checkout, copy `composer.json` to
`composer.local.json` and add this top-level repository configuration:

```json
"repositories": [
    {
        "type": "path",
        "url": "../framework",
        "options": {
            "symlink": true,
            "versions": {"divergence/divergence": "3.3.0"}
        }
    }
]
```

Copy `composer.lock` to `composer.local.lock`, then run
`COMPOSER=composer.local.json composer update divergence/divergence`. Both local
files are ignored. Use `composer install` to restore the released dependency;
commit only the main Composer manifest and lock file for deployment.
