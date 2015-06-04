<?php
namespace inflector;

class Inflector
{
    /**
     * Contains the list of singluralization rules.
     *
     * @var array An array of regular expression rules in the form of `'match' => 'replace'`,
     *            which specify the matching and replacing rules for the singluralization of words.
     */
    protected static $_singular = [];

    /**
     * Contains the list of pluralization rules.
     *
     * @var array An array of regular expression rules in the form of `'match' => 'replace'`,
     *            which specify the matching and replacing rules for the pluralization of words.
     */
     protected static $_plural = [];

    /**
     * Takes a under_scored word and turns it into a camelcased word.
     *
     * @param  string  $word  An underscored or slugged word (i.e. `'red_bike'` or `'red-bike'`).
     * @param  array   $on    List of characters to camelize on.
     * @return string         Camel cased version of the word (i.e. `'RedBike'`).
     */
    public static function camelize($word)
    {
        $upper = function($matches) {
            return strtoupper($matches[0]);
        };
        $camelized = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', strtolower($word))));
        return preg_replace_callback('/(\\\[a-z])/', $upper, $camelized);
    }

    /**
     * Takes a under_scored word and turns it into a camel-back word.
     *
     * @param  string  $word  An underscored or slugged word (i.e. `'red_bike'` or `'red-bike'`).
     * @param  array   $on    List of characters to camelize on.
     * @return string         Camel-back version of the word (i.e. `'redBike'`).
     */
    public static function camelback($word, $on = ['_', '-', '\\']) {
        return lcfirst(static::camelize($word));
    }

    /**
     * Takes a CamelCased version of a word and turns it into an under_scored one.
     *
     * @param  string $word Camel cased version of a word (i.e. `'RedBike'`).
     * @return string       Underscored version of the word (i.e. `'red_bike'`).
     */
    public static function underscore($word)
    {
        $underscored =  strtr(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $word), '-', '_');
        return strtolower(static::transliterate($underscored));
    }

    /**
     * Replaces underscores with dashes in the string.
     *
     * @param  string $word Underscored string (i.e. `'red_bike'`).
     * @return string       dashes version of the word (i.e. `'red-bike'`).
     */
    public static function dasherize($word)
    {
        return strtr($word, '_', '-');
    }

    /**
     * Returns a string with all spaces converted to given replacement and non word characters removed.
     * Maps special characters to ASCII using `transliterator_transliterate`.
     *
     * @param  string $string      An arbitrary string to convert.
     * @param  string $replacement The replacement to use for spaces.
     * @return string              The converted string.
     */
    public static function slug($string, $replacement = '-')
    {
        $transliterated = static::transliterate($string);
        $spaced = preg_replace('/[^\w\s]/', ' ', $transliterated);
        return preg_replace('/\\s+/', $replacement, trim($spaced));
    }

    /**
     * Returns a lowercased string with all spaces converted to given replacement and non word characters removed.
     * Maps special characters to ASCII using `transliterator_transliterate`.
     *
     * @param  string $string      An arbitrary string to convert.
     * @param  string $replacement The replacement to use for spaces.
     * @return string              The converted lowercased string.
     */
    public static function parameterize($string, $replacement = '-')
    {
        $transliterated = static::transliterate($string);
        return strtolower(static::slug($string, $replacement));
    }

    /**
     * Takes an under_scored version of a word and turns it into an human- readable form by
     * replacing underscores with a space, and by upper casing the initial character of each word.
     *
     * @param  string $word      Under_scored version of a word (i.e. `'red_bike'`).
     * @param  string $separator The separator character used in the initial string.
     * @return string            Human readable version of the word (i.e. `'Red Bike'`).
     */
    public static function titleize($word, $separator = '_') {
        return ucwords(static::humanize($word, $separator));
    }

    /**
     * Takes an under_scored version of a word and turns it into an human- readable form by
     * replacing underscores with a space, and by upper casing the initial character of the sentence.
     *
     * @param  string $word      Under_scored version of a word (i.e. `'red_bike'`).
     * @param  string $separator The separator character used in the initial string.
     * @return ucfirst(string            Human readable version of the word (i.e. `'Red bike'`).
     */
    public static function humanize($word, $separator = '_')
    {
        return ucfirst(strtr(preg_replace('/_id$/', '', $word), $separator, ' '));
    }

    /**
     * Set a new pluralization rule and its replacement.
     *
     * @param string $rule        A regular expression.
     * @param string $replacement The replacement expression.
     * @param string $locale      The locale where this rule will be applied.
     */
    public static function plural($rule, $replacement, $locale = 'auto')
    {
        static::_inflect('_plural', $rule, $replacement, $locale);
    }

    /**
     * Set a new singularization rule and its replacement.
     *
     * @param string $rule        A regular expression.
     * @param string $replacement The replacement expression.
     * @param string $locale      The locale where this rule will be applied.
     */
    public static function singular($rule, $replacement, $locale = 'auto')
    {
        static::_inflect('_singular', $rule, $replacement, $locale);
    }

    /**
     * Set a new inflection rule and its replacement.
     *
     * @param string $type        The inflection type.
     * @param string $rule        A regular expression.
     * @param string $replacement The replacement expression.
     * @param string $locale      The locale where this rule will be applied.
     */
    protected static function _inflect($type, $rule, $replacement, $locale)
    {
        $rules = & static::${$type};
        if (!isset($rules[$locale])) {
            $rules[$locale] = [];
        }
        $rules[$locale] = [$rule => $replacement] + $rules[$locale];
    }

    /**
     * Changes the form of a word from singular to plural.
     *
     * @param  string $word   Word in singular form.
     * @param  string $locale The locale to use for rules. Defaults to `'auto'`.
     * @return string         Word in plural form.
     */
    public static function pluralize($word, $locale = 'auto')
    {
        $rules = static::$_plural;
        return static::_inflectize($rules, $word, $locale);
    }

    /**
     * Changes the form of a word from plural to singular.
     *
     * @param  string $word   Word in plural form.
     * @param  string $locale The locale to use for rules. Defaults to `'auto'`.
     * @return string         Word in plural form.
     */
    public static function singularize($word, $locale = 'auto')
    {
        $rules = static::$_singular;
        return static::_inflectize($rules, $word, $locale);
    }

    /**
     * Changes the form of a word.
     *
     * @param  string $rules  The inflection rules array.
     * @param  string $word   A word.
     * @param  string $locale The locale to use for rules.
     * @return string         The inflectized word.
     */
    protected static function _inflectize($rules, $word, $locale)
    {
        if (!$word || !isset($rules[$locale])) {
            return $word;
        }
        $result = $word;
        foreach ($rules[$locale] as $rule => $replacement) {
            $result = preg_replace($rule, $replacement, $word, -1, $count);
            if ($count) {
                return $result;
            }
        }
        return $result;
    }

    /**
     * Set a new exception in inflection.
     *
     * @param string $singular The singular form of the word.
     * @param string $plural   The plural form of the word.
     * @param string $locale   The locale where this irregularity will be applied.
     */
    public static function irregular($singular, $plural, $locale = 'auto')
    {
        $rules = !is_array($singular) ? [$singular => $plural] : $singular;

        $len = min(mb_strlen($singular), mb_strlen($plural));

        $prefix = '';
        $index = 0;
        while ($index < $len && ($singular[$index] === $plural[$index])) {
            $prefix .= $singular[$index];
            $index++;
        }
        if (!$sSuffix = substr($singular, $index)) {
            $sSuffix = '';
        }
        if (!$pSuffix = substr($plural, $index)) {
            $pSuffix = '';
        }

        static::singular("/({$singular})$/i", "\\1", $locale);
        static::singular("/({$prefix}){$pSuffix}$/i", "\\1{$sSuffix}", $locale);
        static::plural("/({$plural})$/i", "\\1", $locale);
        static::plural("/({$prefix}){$sSuffix}$/i", "\\1{$pSuffix}", $locale);
    }

    /**
     * Replaces non-ASCII characters with an ASCII approximation.
     *
     * @param  string $string
     * @param  string $transliterator
     * @return string
     */
    public static function transliterate($string, $transliterator = "Any-Latin; Latin-ASCII; [\u0080-\u7fff] remove;")
    {
        return transliterator_transliterate($transliterator, $string);
    }

    /**
     * Init the Inflector class with some locale based initializer
     *
     * @param string  $locale  The locale to load.
     * @param closure $closure A custom closure.
     */
    public static function load($locale = 'auto', $closure = null)
    {
        if ($closure === null) {
            $path = __DIR__ . DIRECTORY_SEPARATOR . 'inflection' . DIRECTORY_SEPARATOR . "{$locale}.php";
            if (file_exists($path)) {
                $closure = require $path;
            }
        }
        if (!is_callable($closure)) {
            throw new InflectorException("Error, unable to load the `'{$locale}'` locale .");
        }
        $closure($locale);
    }

    /**
     * Clears all inflection rules.
     */
    public static function reset()
    {
        static::$_singular = [];
        static::$_plural = [];
    }

}
Inflector::load();
