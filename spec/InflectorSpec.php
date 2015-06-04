<?php
namespace inflector\spec\util;

use inflector\InflectorException;
use inflector\Inflector;
use kahlan\plugin\Stub;

describe("Inflector", function() {

    describe("->transliterate()", function() {

        it("transliterates a string", function() {

            $result = Inflector::transliterate("A æ Übérmensch på høyeste nivå! И я люблю PHP! есть. ﬁ");
            expect($result)->toBe('A ae Ubermensch pa hoyeste niva! I a lublu PHP! est. fi');

            $result = Inflector::transliterate('の話が出たので大丈夫かなあと');
            expect($result)->toBe('no huaga chutanode da zhang fukanaato');

        });

    });

    describe("->slug()", function() {

        it("slugs a string", function() {
            $result = Inflector::slug('Foo:Bar & Cie');
            expect($result)->toBe('Foo-Bar-Cie');

            $result = Inflector::slug('Foo:Bar & Cie', '_');
            expect($result)->toBe('Foo_Bar_Cie');
        });

    });

    describe("->parameterize()", function() {

        it("parameterizes a string", function() {
            $result = Inflector::parameterize('Foo:Bar & Cie');
            expect($result)->toBe('foo-bar-cie');

            $result = Inflector::parameterize('Foo:Bar & Cie', '_');
            expect($result)->toBe('foo_bar_cie');
        });

    });

    describe("->underscore()", function() {

        it("underscores a string", function() {

            expect(Inflector::underscore('ClassName'))->toBe('class_name');
            expect(Inflector::underscore('TestField'))->toBe('test_field');
            expect(Inflector::underscore('MyName\Space'))->toBe('my_name\space');
            expect(Inflector::underscore('dashed-version'))->toBe('dashed_version');

        });

    });

    describe("->dasherize()", function() {

        it("dasherizes a string", function() {

            expect(Inflector::dasherize('class_name'))->toBe('class-name');
            expect(Inflector::dasherize('test_field'))->toBe('test-field');

        });

    });

    describe("->camelize()", function() {

        it("camelizes a string", function() {

            expect(Inflector::camelize('test-field'))->toBe('TestField');
            expect(Inflector::camelize('test_field'))->toBe('TestField');
            expect(Inflector::camelize('TEST_FIELD'))->toBe('TestField');
            expect(Inflector::camelize('my_name\space'))->toBe('MyName\Space');

        });

    });

    describe("->camelback()", function() {

        it("camelbacks a string", function() {

            expect(Inflector::camelback('test-field'))->toBe('testField');
            expect(Inflector::camelback('test field'))->toBe('testField');
            expect(Inflector::camelback('TEST_FIELD'))->toBe('testField');
            expect(Inflector::camelback('my_name\space'))->toBe('myName\Space');

        });

    });

    describe("->titleize()", function() {

        it("titleizes a string", function() {

            expect(Inflector::titleize('posts'))->toBe('Posts');
            expect(Inflector::titleize('posts_tags'))->toBe('Posts Tags');
            expect(Inflector::titleize('file_systems'))->toBe('File Systems');
            expect(Inflector::titleize('the-post-title', '-'))->toBe('The Post Title');

        });

    });

    describe("->humanize()", function() {

        it("humanizes a string", function() {

            expect(Inflector::humanize('posts'))->toBe('Posts');
            expect(Inflector::humanize('post_id'))->toBe('Post');
            expect(Inflector::humanize('posts_tags'))->toBe('Posts tags');
            expect(Inflector::humanize('file_systems'))->toBe('File systems');
            expect(Inflector::humanize('the-post-title', '-'))->toBe('The post title');

        });

    });

    describe("->plural()", function() {

        beforeEach(function() {

            Inflector::reset();
            Inflector::load();

        });

        it("adds a new pluralization rule", function() {

            Inflector::plural('/(bye)$/i', 'Good \1');
            expect(Inflector::pluralize('bye'))->toBe('Good bye');
        });

        it("adds a new pluralization rule only in a specified locale", function() {

            Inflector::plural('/(bye)$/i', 'Good \1', 'fr');
            expect(Inflector::pluralize('bye'))->not->toBe('Good bye');
            expect(Inflector::pluralize('bye', 'fr'))->toBe('Good bye');

        });

    });

    describe("->singular()", function() {

        beforeEach(function() {

            Inflector::reset();
            Inflector::load();

        });

        it("adds a new singularization rule", function() {

            Inflector::singular('/(bye)$/i', 'Good \1');
            expect(Inflector::singularize('bye'))->toBe('Good bye');
        });

        it("adds a new singularization rule only in a specified locale", function() {

            Inflector::singular('/(bye)$/i', 'Good \1', 'fr');
            expect(Inflector::singularize('bye'))->not->toBe('Good bye');
            expect(Inflector::singularize('bye', 'fr'))->toBe('Good bye');

        });

    });

    describe("->irregular()", function() {

        beforeEach(function() {

            Inflector::reset();
            Inflector::load();

        });

        it("adds a new irregularity", function() {

            Inflector::irregular('nexus', 'nexxus');
            expect(Inflector::singularize('nexxus'))->toBe('nexus');
            expect(Inflector::pluralize('nexus'))->toBe('nexxus');
        });

        it("adds a new irregularity only in a specified locale", function() {

            Inflector::irregular('nexus', 'nexxus', 'fr');
            expect(Inflector::singularize('nexxus'))->not->toBe('nexus');
            expect(Inflector::pluralize('nexus'))->not->toBe('nexxus');
            expect(Inflector::singularize('nexxus', 'fr'))->toBe('nexus');
            expect(Inflector::pluralize('nexus', 'fr'))->toBe('nexxus');

        });

    });

    context("using english", function() {

        beforeEach(function() {

            Inflector::reset();
            Inflector::load();

        });

        describe("->pluralize()", function() {

            it("pluralizes empty word", function() {

                expect(Inflector::pluralize(''))->toBe('');

            });

            it("pluralizes words", function() {

                expect(Inflector::pluralize('post'))->toBe('posts');
                expect(Inflector::pluralize('posts'))->toBe('posts');

                expect(Inflector::pluralize('comment'))->toBe('comments');
                expect(Inflector::pluralize('comments'))->toBe('comments');

            });

            it("pluralizes words ending in 'x','z','ss','ch' or 'sh'", function() {

                expect(Inflector::pluralize('tax'))->toBe('taxes');
                expect(Inflector::pluralize('taxes'))->toBe('taxes');

                expect(Inflector::pluralize('buzz'))->toBe('buzzes');
                expect(Inflector::pluralize('buzzes'))->toBe('buzzes');

                expect(Inflector::pluralize('address'))->toBe('addresses');
                expect(Inflector::pluralize('addresses'))->toBe('addresses');

                expect(Inflector::pluralize('catch'))->toBe('catches');
                expect(Inflector::pluralize('catches'))->toBe('catches');

                expect(Inflector::pluralize('dish'))->toBe('dishes');
                expect(Inflector::pluralize('dishes'))->toBe('dishes');

            });

            it("pluralizes words ending with 'y' preceded by a consonant", function() {

                expect(Inflector::pluralize('pony'))->toBe('ponies');
                expect(Inflector::pluralize('ponies'))->toBe('ponies');

            });

            it("pluralizes words ending with 'meta' or 'data'", function() {

                expect(Inflector::pluralize('meta'))->toBe('meta');
                expect(Inflector::pluralize('data'))->toBe('data');
                expect(Inflector::pluralize('metadata'))->toBe('metadata');

            });

            it("keeps at least the case sensitiveness of common prefix", function() {

                expect(Inflector::pluralize('pOSt'))->toBe('pOSts');
                expect(Inflector::pluralize('pOnY'))->toBe('pOnies');
                expect(Inflector::pluralize('tAX'))->toBe('tAXes');
                expect(Inflector::pluralize('META'))->toBe('META');

            });

            it("pluralizes suffixes", function() {

                expect(Inflector::pluralize('ArticlePost'))->toBe('ArticlePosts');
                expect(Inflector::pluralize('ContactPerson'))->toBe('ContactPeople');
                expect(Inflector::pluralize('JobTax'))->toBe('JobTaxes');
                expect(Inflector::pluralize('ImageMETA'))->toBe('ImageMETA');

            });

            it("pluralizes execeptions", function() {

                expect(Inflector::pluralize('child'))->toBe('children');
                expect(Inflector::pluralize('children'))->toBe('children');

                expect(Inflector::pluralize('equipment'))->toBe('equipment');

                expect(Inflector::pluralize('information'))->toBe('information');

                expect(Inflector::pluralize('man'))->toBe('men');
                expect(Inflector::pluralize('men'))->toBe('men');

                expect(Inflector::pluralize('news'))->toBe('news');

                expect(Inflector::pluralize('person'))->toBe('people');
                expect(Inflector::pluralize('people'))->toBe('people');

                expect(Inflector::pluralize('woman'))->toBe('women');
                expect(Inflector::pluralize('women'))->toBe('women');

            });

        });

        describe("->singularize()", function() {

            it("singularizes empty word", function() {

                expect(Inflector::singularize(''))->toBe('');

            });

            it("singularizes words", function() {

                expect(Inflector::singularize(''))->toBe('');

                expect(Inflector::singularize('posts'))->toBe('post');
                expect(Inflector::singularize('post'))->toBe('post');

                expect(Inflector::singularize('comments'))->toBe('comment');
                expect(Inflector::singularize('comment'))->toBe('comment');

            });

            it("singularizes words ending in 'x','z','ss','ch' or 'sh'", function() {

                expect(Inflector::singularize('taxes'))->toBe('tax');
                expect(Inflector::singularize('tax'))->toBe('tax');

                expect(Inflector::singularize('buzzes'))->toBe('buzz');
                expect(Inflector::singularize('buzz'))->toBe('buzz');

                expect(Inflector::singularize('addresses'))->toBe('address');
                expect(Inflector::singularize('address'))->toBe('address');

                expect(Inflector::singularize('catches'))->toBe('catch');
                expect(Inflector::singularize('catch'))->toBe('catch');

                expect(Inflector::singularize('dishes'))->toBe('dish');
                expect(Inflector::singularize('dish'))->toBe('dish');

            });

            it("singularizes words ending with 'y' preceded by a consonant", function() {

                expect(Inflector::singularize('ponies'))->toBe('pony');
                expect(Inflector::singularize('pony'))->toBe('pony');

            });

            it("keeps at least the case sensitiveness of common prefix", function() {

                expect(Inflector::singularize('pOSts'))->toBe('pOSt');
                expect(Inflector::singularize('pOniEs'))->toBe('pOny');
                expect(Inflector::singularize('tAXes'))->toBe('tAX');
                expect(Inflector::singularize('META'))->toBe('META');

            });

            it("singularizes suffixes", function() {

                expect(Inflector::singularize('ArticlePosts'))->toBe('ArticlePost');
                expect(Inflector::singularize('ContactPeople'))->toBe('ContactPerson');
                expect(Inflector::singularize('JobTaxes'))->toBe('JobTax');
                expect(Inflector::singularize('ImageMETA'))->toBe('ImageMETA');

            });

            it("singularizes execeptions", function() {

                expect(Inflector::singularize('children'))->toBe('child');
                expect(Inflector::singularize('child'))->toBe('child');

                expect(Inflector::singularize('equipment'))->toBe('equipment');

                expect(Inflector::singularize('information'))->toBe('information');

                expect(Inflector::singularize('men'))->toBe('man');
                expect(Inflector::singularize('man'))->toBe('man');

                expect(Inflector::singularize('news'))->toBe('news');

                expect(Inflector::singularize('people'))->toBe('person');
                expect(Inflector::singularize('person'))->toBe('person');

                expect(Inflector::singularize('women'))->toBe('woman');
                expect(Inflector::singularize('woman'))->toBe('woman');

            });

        });

    });

    describe("->reset()", function() {

        it("clears all the inflector rules", function() {

            Inflector::reset();
            expect(Inflector::singularize('posts'))->toBe('posts');
            expect(Inflector::pluralize('post'))->toBe('post');

        });

    });

    describe("->load()", function() {

        it("loads the english inflection rules", function() {

            Inflector::load();
            expect(Inflector::singularize('posts'))->toBe('post');
            expect(Inflector::pluralize('post'))->toBe('posts');

        });

        it("loads the french inflection rules", function() {

            Inflector::load('fr');
            expect(Inflector::singularize('bateaux', 'fr'))->toBe('bateau');
            expect(Inflector::pluralize('bateau', 'fr'))->toBe('bateaux');

        });

        it("loads the spanish inflection rules", function() {

            Inflector::load('es');
            expect(Inflector::singularize('ediciones', 'es'))->toBe('edición');
            expect(Inflector::pluralize('edición', 'es'))->toBe('ediciones');

        });

        it("loads inflection rules from a closure definition", function() {

            Inflector::load('zz', function() {
                Inflector::singular('/x$/i', '', 'zz');
                Inflector::plural('/([^x])$/i', '\1x', 'zz');
            });
            expect(Inflector::singularize('abcdefx', 'zz'))->toBe('abcdef');
            expect(Inflector::pluralize('abcdef', 'zz'))->toBe('abcdefx');

        });

        it("loads the english, french and spanish inflection rules", function() {

            Inflector::load();
            Inflector::load('fr');
            Inflector::load('es');

            expect(Inflector::singularize('taxes'))->toBe('tax');
            expect(Inflector::pluralize('tax'))->toBe('taxes');

            expect(Inflector::singularize('bateaux', 'fr'))->toBe('bateau');
            expect(Inflector::pluralize('bateau', 'fr'))->toBe('bateaux');

            expect(Inflector::singularize('ediciones', 'es'))->toBe('edición');
            expect(Inflector::pluralize('edición', 'es'))->toBe('ediciones');

        });

        it("returns an exception for invalid locales", function() {

            $closure = function() {
                Inflector::load("goa'uld");
            };
            expect($closure)->toThrow(new InflectorException("Error, unable to load the `'goa'uld'` locale ."));

        });

        it("returns an exception for invalid closures", function() {

            $closure = function() {
                Inflector::load("auto", "not/a/closure");
            };
            expect($closure)->toThrow(new InflectorException("Error, unable to load the `'auto'` locale ."));

        });

    });

});
