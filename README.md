<!-- Screenshot -->
<p align="center">
    <img src="resources/example.png" alt="Code example">
</p>

<!-- Badges -->
<p align="center">
  <img src="resources/build.svg" alt="Build">
  <img src="resources/coverage.svg" alt="Coverage">
  <img src="resources/version.svg" alt="Version">
  <img src="resources/license.svg" alt="License">
</p>

# Quest

This package enables pseudo fuzzy-searching within Laravel database and Eloquent queries. Due to its pattern matching methods, it only supports **MySQL** or **MariaDB**, though I welcome any PRs to enable support for databases like Postgres.

Much of this library is based on the fantastic work of Tom Lingham for the now abandoned [Laravel Searchy](https://github.com/TomLingham/Laravel-Searchy) package. If you're interested in the background of how the fuzzy searching works, check out the readme for that project.

## Installation

Pull in the package using composer

```bash
composer require mattkingshott/quest
```

## Usage

Quest automatically registers a service provider containing several macros. These macros are then attached to the underlying `Illuminate\Database\Query\Builder` class.

### Filtering results

You can perform a fuzzy-search by calling the `whereFuzzy` method. This method takes two parameters. The first, is the field name. The second, is the value to use for the search e.g.

```php
DB::table('users')
  ->whereFuzzy('name', 'jd') // matches John Doe
  ->first();

User::whereFuzzy('name', 'jd') // matches John Doe
    ->first();
```

You can also perform a fuzzy search across multiple columns by chaining several `whereFuzzy` method calls:

```php
User::whereFuzzy('name', 'jd')  // matches John Doe
    ->whereFuzzy('email', 'gm') // matches @gmail.com
    ->first();
```

### Ordering results

When using Quest, a `'relevance_*'` column will be included in your search results. The `*` is a wildcard that will be replaced with the name of the field that you are searching on e.g.

```php
User::whereFuzzy('email', 'gm') // relevance_email
```

This column contains the score that the record received after each of the fuzzy-searching pattern matchers were applied to it. The higher the score, the more closely the record matches the search term.

Of course, you'll want to order the results so that the records with the highest score appear first. To make this easier, Quest includes an `orderByFuzzy` helper method that wraps the relevant `orderBy` clauses:

```php
User::whereFuzzy('name', 'jd')
    ->orderByFuzzy('name')
    ->first();

// Equivalent to:

User::whereFuzzy('name', 'jd')
    ->orderBy('relevance_name', 'desc')
    ->first();
```

If you are searching across multiple fields, you can provide an `array` to the `orderByFuzzy` method:

```php
User::whereFuzzy('name', 'jd')
    ->whereFuzzy('email', 'gm')
    ->orderByFuzzy(['name', 'email'])
    ->first();

// Equivalent to:

User::whereFuzzy('name', 'jd')
    ->orderBy('relevance_name', 'desc')
    ->orderBy('relevance_email', 'desc')
    ->first();
```
### Setting Minimum Match Threshold

When using quest an overall score will be assigned to each row `_fuzzy_relevance_` with a range of 0-100.

You can enforce a minimum match standard and limit results returned by using `->atLeastFuzzy()`

```php
User::whereFuzzy('name', 'jd')
    ->atLeastFuzzy(70)
    ->first();

// Equivalent to:

User::whereFuzzy('name', 'jd')
    ->having('_fuzzy_relevance_', '>',  70)
    ->first();
```

## Limitations

It is not possible to use the `paginate` method with Quest as the relevance fields are omitted from the secondary query that Laravel runs to get the count of the records required for `LengthAwarePaginator`. However, you can use the `simplePaginate` method without issue. In many cases this a more preferable option anyway, particularly when dealing with large datasets as the `paginate` method becomes slow when scrolling through large numbers of pages.

## Contributing

Thank you for considering a contribution to Quest. You are welcome to submit a PR containing improvements, however if they are substantial in nature, please also be sure to include a test or tests.

## Support the project

If you'd like to support the development of Quest, then please consider [sponsoring me](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=YBEHLHPF3GUVY&source=url). Thanks so much!

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
