# Inflector - Inflector library

[![Build Status](https://travis-ci.org/crysalead/inflector.png?branch=master)](https://travis-ci.org/crysalead/inflector)
[![Code Coverage](https://scrutinizer-ci.com/g/crysalead/inflector/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/crysalead/inflector/)

Inflector is a small library that can perform string transformation like singularization, pluralization, underscore to camel case, titelize words
and more. Inflections can be localized.

The `Inflector` class is prepopulated with english inflections for singularization and pluralization to be ready to use.

### Usage

#### Examples of usage of the inflector with the `'default'` locale (i.e english):

```php

use Lead\Inflector\Inflector;

# pluralize

Inflector::pluralize('post');                       // "posts"
Inflector::pluralize('posts');                      // "posts"
Inflector::pluralize('child');                      // "children"
Inflector::pluralize('ContactPerson');              // "ContactPeople"

# singularize

Inflector::singularize('posts');                    // "post"
Inflector::singularize('children');                 // "child"
Inflector::singularize('ContactPeople');            // "ContactPerson"

# transliterate

Inflector::transliterate('の話が出たので大丈夫かなあと'); // "no huaga chutanode da zhang fukanaato"

# slug

Inflector::slug('Foo:Bar & Cie');                   // "Foo-Bar-Cie"
Inflector::slug('Foo:Bar & Cie', '_');              // "Foo_Bar_Cie"

# parameterize

Inflector::parameterize('Foo:Bar & Cie');           // "foo-bar-cie"
Inflector::parameterize('Foo:Bar & Cie', '_');      // "foo_bar_cie"

# camelize

Inflector::camelize('test_field');                  // "TestField"
Inflector::camelize('TEST_FIELD');                  // "TestField"
Inflector::camelize('my_name\space');               // "MyName\Space"

# camelback

Inflector::camelback('test_field');                 // "testField"
Inflector::camelback('TEST_FIELD');                 // "testField"

# underscore

Inflector::underscore('TestField');                 // "test_field"
Inflector::underscore('MyName\Space');              // "my_name\space"
Inflector::underscore('dashed-string');             // "dashed_string"

# dasherize

Inflector::dasherize('underscored_string');         // "underscored_string"

# humanize

Inflector::humanize('employee_salary');             // "Employee salary"
Inflector::humanize('author_id');                   // "Author"

# titleize

Inflector::titleize('man from the boondocks');      // "Man From The Boondocks"
Inflector::titleize('x-men: the last stand');       // "X Men: The Last Stand"
Inflector::titleize('TheManWithoutAPast');          // "The Man Without A Past"
Inflector::titleize('raiders_of_the_lost_ark');     // "Raiders Of The Lost Ark"

```

#### Examples of usage of custom locales:

```php

namespace inflector\Inflector;

Inflector::pluralize('child');                       // "children"

//Load custom definition for `'zz'` locale using a closure
Inflector::singular('/x$/i', '', 'zz');
Inflector::plural('/([^x])$/i', '\1x', 'zz');

Inflector::singularize('abcdefx', 'zz');             // "abcdef"
Inflector::pluralize('abcdef', 'zz');                // "abcdefx"
```

If you wan't to use an other language by default for inflections, you can override the `'default'` rules directly.

```php
Inflector::reset(true); // Remove all loaded inflection rules.

Inflector::singular('/x$/i', '', 'default');
Inflector::plural('/([^x])$/i', '\1x', 'default');

Inflector::singularize('abcdefx');             // "abcdef"
Inflector::pluralize('abcdef');                // "abcdefx"
```

Note: you can check [spec/fixture](https://github.com/crysalead/inflector/tree/master/spec/fixture) for some examples of inflection rules for spanish and french languages.

### Requirement

Requires PHP >= 5.4 and the PECL intl extension.

```
sudo apt-get install php-intl
```

### Installation with Composer

The recommended way to install this package is through [Composer](http://getcomposer.org/).
Create a `composer.json` file and run `composer install` command to install it:

```json
{
	"require":
	{
		"crysalead/inflector": "~1.0"
	}
}
```

### Testing

The spec suite can be runned with:

```
cd inflector
composer install
./bin/kahlan
```

PS: [Composer](http://getcomposer.org/) need to be present on your system.

### Acknowledgements

Most of the code and documentation was adapted from [Ruby On Rails](http://rubyonrails.org/)'s
[Inflector](http://api.rubyonrails.org/classes/ActiveSupport/Inflector.html).
