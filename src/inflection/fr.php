<?php
namespace inflector\inflection;

use inflector\Inflector;

return function($locale) {

    Inflector::singular('/s$/i', '', 'fr');
    Inflector::plural('/([^s])$/i', '\1s', 'fr');

    Inflector::plural('/(eu|eau)$/i', '\1x', 'fr');
    Inflector::singular('/(eu|eau)x$/i', '\1', 'fr');

    Inflector::plural('/(x|z)$/i', '\1', 'fr');

    Inflector::irregular('monsieur', 'messieurs', 'fr');
    Inflector::irregular('madame', 'mesdames', 'fr');
    Inflector::irregular('mademoiselle', 'mesdemoiselles', 'fr');

    /**
     * Warning, using an "exhastive" list of rules will slow
     * down all singularizations/pluralizations generation.
     * So it's preferable to only add the ones you are actually using.
     */
};
