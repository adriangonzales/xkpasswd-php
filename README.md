# xkpasswd

*Memorable password generator, inspired by a PERL module powering xkpasswd.net/s/*

Based heavily on the [xkpasswd-node](https://github.com/vot/xkpasswd-node) package. Also a nod to [battery-staple](https://github.com/geekish/battery-staple) for the inspiration.

# Install

```bash
composer require adgodev/xkpasswd-php
```

# Usage

You can use xkpasswd as a module in your application.

**EXAMPLES**

```php
use Adgodev\Xkpasswd\PasswordGenerator;

PasswordGenerator::generate();

// valuable=bear=difference=53

PasswordGenerator::generate([
    'complexity' => 5,
    'separators' => '#+-'
]);

// addition#wheat#congress#manner#lonely#20

PasswordGenerator::generate([
    'wordList' => 'myWordList.json'
]);

// apple#grape#banana#40
```

## Options

You can specify `complexity` argument in accordance with [complexity levels table](#complexity-levels). Defaults to 2.

If specified `pattern` argument overrides the [pattern](#patterns) derived from complexity level.

If `separators` are provided they are used instead of the standard set (see complexity levels).
One separator is used per password, picked randomly from the provided set.

You can set `transform` option to `alternate` or `uppercase` to trigger case transformation.

To generate multiple passwords at once you can specify the desired
amount with the `number` argument. Defaults to 1.

Finally if you'd like to use a custom list of words you can provide it
as a JSON file, text file or an array via `wordList` function.

**EXAMPLE** Default behaviour

```php
use Adgodev\Xkpasswd\PasswordGenerator;

PasswordGenerator::generate();

// hide+threw+money+61
```

**EXAMPLE** Specify complexity

```php
use Adgodev\Xkpasswd\PasswordGenerator;

PasswordGenerator::generate([
    'complexity' => 5
]);

// join=industrial=wide=direction=lungs=16

PasswordGenerator::generate([
    'complexity' => 6
]);

// 57!FIFTHLY!astronauts!AFFECTEDLY!nymphs!TRUSTLESSNESSES!06
```

**EXAMPLE** Specify custom pattern

```php
use Adgodev\Xkpasswd\PasswordGenerator;

PasswordGenerator::generate([
    'pattern' => 'wdwd'
]);

// adjective3solar6
```

**EXAMPLE** Specify custom word list / dictionary

```php
use Adgodev\Xkpasswd\PasswordGenerator;

PasswordGenerator::generate([
    'wordList' => 'myWordList.json'
]);

// orange.apple.banana
```

```php
use Adgodev\Xkpasswd\PasswordGenerator;

PasswordGenerator::generate([
    'wordList' => 'myWordList.txt'
]);

// kiwi-strawberry-grape
```

## Patterns

Patterns can consist of any combination of words, digits and separators.
The first letters (**w**, **d** and **s** respectively) are used in pattern string provided to the password generation function.

For example:

* `w` will return a single word (i.e. `demographics`). Use `w` for lowercase and `W` for uppercase.
* `wsd` will return a word and a digit, separated by one of the permitted separators (i.e. `storm#7`)
* `wswsdd` will return two words followed by a two digit number, all with separators between (i.e. `delates+dissembled+16`)



## Complexity levels

There are 6 complexity levels specified which can be used to provide
default patterns as well as trigger additional features, such as alternate casing
between words and expanded sets of separators.


| Complexity | Pattern         | Separators       |
|------------|-----------------|------------------|
| 1          | wsw             | #.-=+_           |
| 2          | wswsw           | #.-=+_           |
| 3          | wswswsdd        | #.-=+_           |
| 4          | wswswswsdd      | #.-=+_           |
| 5          | wswswswswsd     | #.-=+_!$*:~?     |
| 6          | ddswswswswswsdd | #.-=+_!$*:~?%^&; |

In addition level 6 alternates upper and lower case between words.

## Release notes

v1.0.0

Initial release
