<?php
namespace Lead\Inflector;

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
        $word = preg_replace('/([a-z])([A-Z])/', '$1_$2', $word);
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
    public static function plural($rule, $replacement, $locale = 'default')
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
    public static function singular($rule, $replacement, $locale = 'default')
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
     * @param  string $locale The locale to use for rules. Defaults to `'default'`.
     * @return string         Word in plural form.
     */
    public static function pluralize($word, $locale = 'default')
    {
        $rules = static::$_plural;
        return static::_inflectize($rules, $word, $locale);
    }

    /**
     * Changes the form of a word from plural to singular.
     *
     * @param  string $word   Word in plural form.
     * @param  string $locale The locale to use for rules. Defaults to `'default'`.
     * @return string         Word in plural form.
     */
    public static function singularize($word, $locale = 'default')
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
    public static function irregular($singular, $plural, $locale = 'default')
    {
        $rules = !is_array($singular) ? [$singular => $plural] : $singular;

        $len = min(strlen($singular), strlen($plural));

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
     * Clears all inflection rules.
     *
     * @param string|boolean $lang The language name to reset or `true` to reset all even defaults.
     */
    public static function reset($lang = null)
    {
        if (is_string($lang)) {
            unset(static::$_singular[$lang]);
            unset(static::$_plural[$lang]);
            return;
        }
        static::$_singular = [];
        static::$_plural = [];

        if ($lang === true) {
            return;
        }

        /**
         * Initilalize the class with english inflector rules.
         */
        Inflector::singular('/([^s])s$/i', '\1', 'default');
        Inflector::plural('/([^s])$/i', '\1s', 'default');

        Inflector::singular('/(x|z|s|ss|ch|sh)es$/i', '\1', 'default');
        Inflector::plural('/(x|z|ss|ch|sh)$/i', '\1es', 'default');

        Inflector::singular('/ies$/i', 'y', 'default');
        Inflector::plural('/([^aeiouy]|qu)y$/i', '\1ies', 'default');

        Inflector::plural('/(meta|data)$/i', '\1', 'default');

        Inflector::irregular('child', 'children', 'default');
        Inflector::irregular('equipment', 'equipment', 'default');
        Inflector::irregular('information', 'information', 'default');
        Inflector::irregular('man', 'men', 'default');
        Inflector::irregular('news', 'news', 'default');
        Inflector::irregular('person', 'people', 'default');
        Inflector::irregular('woman', 'women', 'default');

        /**
         * Warning, using an "exhastive" list of rules will slow
         * down all singularizations/pluralizations generations.
         * So it may be preferable to only add the ones you are actually needed.
         *
         * Anyhow bellow a list english exceptions which are not covered by the above rules.
         */
        // Inflector::irregular('advice', 'advice', 'default');
        // Inflector::irregular('aircraft', 'aircraft', 'default');
        // Inflector::irregular('alias', 'aliases', 'default');
        // Inflector::irregular('alga', 'algae', 'default');
        // Inflector::irregular('alumna', 'alumnae', 'default');
        // Inflector::irregular('alumnus', 'alumni', 'default');
        // Inflector::irregular('analysis', 'analyses', 'default');
        // Inflector::irregular('antenna', 'antennae', 'default');
        // Inflector::irregular('automaton', 'automata', 'default');
        // Inflector::irregular('axis', 'axes', 'default');
        // Inflector::irregular('bacillus', 'bacilli', 'default');
        // Inflector::irregular('bacterium', 'bacteria', 'default');
        // Inflector::irregular('barracks', 'barracks', 'default');
        // Inflector::irregular('basis', 'bases', 'default');
        // Inflector::irregular('bellows', 'bellows', 'default');
        // Inflector::irregular('buffalo', 'buffaloes', 'default');
        // Inflector::irregular('bus', 'buses', 'default');
        // Inflector::irregular('bison', 'bison', 'default');
        // Inflector::irregular('cactus', 'cacti', 'default');
        // Inflector::irregular('cafe', 'cafes', 'default');
        // Inflector::irregular('calf', 'calves', 'default');
        // Inflector::irregular('cargo', 'cargoes', 'default');
        // Inflector::irregular('cattle', 'cattle', 'default');
        // Inflector::irregular('child', 'children', 'default');
        // Inflector::irregular('congratulations', 'congratulations', 'default');
        // Inflector::irregular('corn', 'corn', 'default');
        // Inflector::irregular('crisis', 'crises', 'default');
        // Inflector::irregular('criteria', 'criterion', 'default');
        // Inflector::irregular('curriculum', 'curricula', 'default');
        // Inflector::irregular('datum', 'data', 'default');
        // Inflector::irregular('deer', 'deer', 'default');
        // Inflector::irregular('die', 'dice', 'default');
        // Inflector::irregular('dregs', 'dregs', 'default');
        // Inflector::irregular('duck', 'duck', 'default');
        // Inflector::irregular('echo', 'echos', 'default');
        // Inflector::irregular('elf', 'elves', 'default');
        // Inflector::irregular('ellipsis', 'ellipses', 'default');
        // Inflector::irregular('embargo', 'embargoes', 'default');
        // Inflector::irregular('equipment', 'equipment', 'default');
        // Inflector::irregular('erratum', 'errata', 'default');
        // Inflector::irregular('evidence', 'evidence', 'default');
        // Inflector::irregular('eyeglasses', 'eyeglasses', 'default');
        // Inflector::irregular('fish', 'fish', 'default');
        // Inflector::irregular('focus', 'foci', 'default');
        // Inflector::irregular('foot', 'feet', 'default');
        // Inflector::irregular('fungus', 'fungi', 'default');
        // Inflector::irregular('gallows', 'gallows', 'default');
        // Inflector::irregular('genus', 'genera', 'default');
        // Inflector::irregular('goose', 'geese', 'default');
        // Inflector::irregular('gold', 'gold', 'default');
        // Inflector::irregular('grotto', 'grottoes', 'default');
        // Inflector::irregular('gymnasium', 'gymnasia', 'default');
        // Inflector::irregular('half', 'halves', 'default');
        // Inflector::irregular('headquarters', 'headquarters', 'default');
        // Inflector::irregular('hoof', 'hooves', 'default');
        // Inflector::irregular('hypothesis', 'hypotheses', 'default');
        // Inflector::irregular('information', 'information', 'default');
        // Inflector::irregular('graffito', 'graffiti', 'default');
        // Inflector::irregular('half', 'halves', 'default');
        // Inflector::irregular('hero', 'heroes', 'default');
        // Inflector::irregular('jewelry', 'jewelry', 'default');
        // Inflector::irregular('kin', 'kin', 'default');
        // Inflector::irregular('knife', 'knives', 'default');
        // Inflector::irregular('larva', 'larvae', 'default');
        // Inflector::irregular('leaf', 'leaves', 'default');
        // Inflector::irregular('legislation', 'legislation', 'default');
        // Inflector::irregular('life', 'lives', 'default');
        // Inflector::irregular('loaf', 'loaves', 'default');
        // Inflector::irregular('locus', 'loci', 'default');
        // Inflector::irregular('louse', 'lice', 'default');
        // Inflector::irregular('luck', 'luck', 'default');
        // Inflector::irregular('luggage', 'luggage', 'default');
        // Inflector::irregular('man', 'men', 'default');
        // Inflector::irregular('mathematics', 'mathematics', 'default');
        // Inflector::irregular('matrix', 'matrices', 'default');
        // Inflector::irregular('means', 'means', 'default');
        // Inflector::irregular('measles', 'measles', 'default');
        // Inflector::irregular('medium', 'media', 'default');
        // Inflector::irregular('memorandum', 'memoranda', 'default');
        // Inflector::irregular('money', 'monies', 'default');
        // Inflector::irregular('moose', 'moose', 'default');
        // Inflector::irregular('mosquito', 'mosquitoes', 'default');
        // Inflector::irregular('motto', 'mottoes', 'default');
        // Inflector::irregular('mouse', 'mice', 'default');
        // Inflector::irregular('mumps', 'mumps', 'default');
        // Inflector::irregular('music', 'music', 'default');
        // Inflector::irregular('mythos', 'mythoi', 'default');
        // Inflector::irregular('nebula', 'nebulae', 'default');
        // Inflector::irregular('neurosis', 'neuroses', 'default');
        // Inflector::irregular('news', 'news', 'default');
        // Inflector::irregular('nucleus', 'nuclei', 'default');
        // Inflector::irregular('numen', 'numina', 'default');
        // Inflector::irregular('oasis', 'oases', 'default');
        // Inflector::irregular('oats', 'oats', 'default');
        // Inflector::irregular('octopus', 'octopuses', 'default');
        // Inflector::irregular('offspring', 'offspring', 'default');
        // Inflector::irregular('ovum', 'ova', 'default');
        // Inflector::irregular('ox', 'oxen', 'default');
        // Inflector::irregular('pajamas', 'pajamas', 'default');
        // Inflector::irregular('pants', 'pants', 'default');
        // Inflector::irregular('paralysis', 'paralyses', 'default');
        // Inflector::irregular('parenthesis', 'parentheses', 'default');
        // Inflector::irregular('person', 'people', 'default');
        // Inflector::irregular('phenomenon', 'phenomena', 'default');
        // Inflector::irregular('pike', 'pike', 'default');
        // Inflector::irregular('plankton', 'plankton', 'default');
        // Inflector::irregular('pliers', 'pliers', 'default');
        // Inflector::irregular('polyhedron', 'polyhedra', 'default');
        // Inflector::irregular('potato', 'potatoes', 'default');
        // Inflector::irregular('quiz', 'quizzes', 'default');
        // Inflector::irregular('radius', 'radii', 'default');
        // Inflector::irregular('roof', 'roofs', 'default');
        // Inflector::irregular('salmon', 'salmon', 'default');
        // Inflector::irregular('scarf', 'scarves', 'default');
        // Inflector::irregular('scissors', 'scissors', 'default');
        // Inflector::irregular('self', 'selves', 'default');
        // Inflector::irregular('series', 'series', 'default');
        // Inflector::irregular('shears', 'shears', 'default');
        // Inflector::irregular('sheep', 'sheep', 'default');
        // Inflector::irregular('shelf', 'shelves', 'default');
        // Inflector::irregular('shorts', 'shorts', 'default');
        // Inflector::irregular('silver', 'silver', 'default');
        // Inflector::irregular('species', 'species', 'default');
        // Inflector::irregular('squid', 'squid', 'default');
        // Inflector::irregular('stimulus', 'stimuli', 'default');
        // Inflector::irregular('stratum', 'strata', 'default');
        // Inflector::irregular('swine', 'swine', 'default');
        // Inflector::irregular('syllabus', 'syllabi', 'default');
        // Inflector::irregular('synopsis', 'synopses', 'default');
        // Inflector::irregular('synthesis', 'syntheses', 'default');
        // Inflector::irregular('tax', 'taxes', 'default');
        // Inflector::irregular('terminus', 'termini', 'default');
        // Inflector::irregular('thesis', 'theses', 'default');
        // Inflector::irregular('thief', 'thieves', 'default');
        // Inflector::irregular('tomato', 'tomatoes', 'default');
        // Inflector::irregular('tongs', 'tongs', 'default');
        // Inflector::irregular('tooth', 'teeth', 'default');
        // Inflector::irregular('torpedo', 'torpedoes', 'default');
        // Inflector::irregular('torus', 'tori', 'default');
        // Inflector::irregular('trousers', 'trousers', 'default');
        // Inflector::irregular('trout', 'trout', 'default');
        // Inflector::irregular('tweezers', 'tweezers', 'default');
        // Inflector::irregular('vertebra', 'vertebrae', 'default');
        // Inflector::irregular('vertex', 'vertices', 'default');
        // Inflector::irregular('vespers', 'vespers', 'default');
        // Inflector::irregular('veto', 'vetoes', 'default');
        // Inflector::irregular('volcano', 'volcanoes', 'default');
        // Inflector::irregular('vortex', 'vortices', 'default');
        // Inflector::irregular('vita', 'vitae', 'default');
        // Inflector::irregular('virus', 'viri', 'default');
        // Inflector::irregular('wheat', 'wheat', 'default');
        // Inflector::irregular('wife', 'wives', 'default');
        // Inflector::irregular('wolf', 'wolves', 'default');
        // Inflector::irregular('woman', 'women', 'default');
        // Inflector::irregular('zero', 'zeros', 'default');
    }
}

Inflector::reset();
