<?php
namespace inflector\inflection;

use inflector\Inflector;

return function($locale) {

    Inflector::singular('/s$/i', '', 'es');
    Inflector::plural('/$/i', 's', 'es');

    Inflector::singular('/es$/i', '', 'es');
    Inflector::plural('/([^aeéiou])$/i', '\1es', 'es');

    Inflector::singular('/ces$/i', 'z', 'es');
    Inflector::plural('/z$/i', 'ces', 'es');

    Inflector::singular('/iones$/i', 'ión', 'es');
    Inflector::plural('/ión$/i', 'iones', 'es');

    Inflector::irregular('carácter', 'caracteres', 'es');

    /**
     * Warning, using an "exhastive" list of rules will slow
     * down all singularizations/pluralizations generation.
     * So it's preferable to only add the ones you are actually using.
     */
};
