<?php
/**
 * @namspace Tests
 * @name TemplateTagTest
 * Summary: #$END$#
 *
 * Date: 2023-02-19
 * Time: 6:04 AM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace Tests;

use HPHIO\Farret\TagFactory;
use HPHIO\Farret\TemplateTag;
use PHPUnit\Framework\TestCase;

class TemplateTagTest extends TestCase
{

    /**
     * @dataProvider providerTestFart
     */
    public function testFart($tag, $find, $expectedMatch, $inDictionary) {
        $expectedReplacement = 'itjazbVSgSBSsxh';

        $TagFactory = new TagFactory();
        $Tag = $TagFactory->getTag($tag);

        if($expectedMatch === false) {
            $this->assertFalse($Tag);
            return;
        }

        $dictionary = [ 'FIRSTNAME' =>  $expectedReplacement];
        $result = $Tag->fart($dictionary);

        $this->assertSame($inDictionary, $result);

        if($inDictionary === true) {
            $this->assertSame($expectedReplacement, $Tag->getReplacement());
        }
    }

    public function providerTestFart() {
        return  [ [ '{{FIRSTNAME}}'   , 'FIRSTNAME' , true  , true  ]
            , [ '{{FIRSTNAME }}'  , 'FIRSTNAME'     , true  , true  ]
            , [ '{{ FIRSTNAME}}'  , 'FIRSTNAME'     , true  , true  ]
            , [ '{{ FIRSTNAME }}' , 'FIRSTNAME'     , true  , true  ]
            , [ '{{ FIRSTNAME }'  , 'FIRSTNAME'     , false , true  ]
            , [ '{ FIRSTNAME }}'  , 'FIRSTNAME'     , false , true  ]
            , [ '{FIRSTNAME }}'   , 'FIRSTNAME'     , false , true  ]
            , [ '{{FIRSTNAME}'    , 'FIRSTNAME'     , false , true  ]
            , [ 'FIRSTNAME }}'    , 'FIRSTNAME'     , false , true  ]
            , [ '{{ FIRSTNAME'    , 'FIRSTNAME'     , false , true  ]
            , [ '{{ LASTNAME }}'  , 'LASTNAME'      , true  , false ]
        ];
    }

    /**
     * @param $tag
     * @param $expectedCount
     * @param $expectedArray
     * @dataProvider providerTestParseArgs
     */

    public function testParseArgs($tag, $expectedCount, $expectedArray) {
        $TagFactory = new TagFactory();
        $tag = $TagFactory->getTag($tag);

        $this->assertCount($expectedCount, $tag->getArgs());

        for($x = 0; $x < count($expectedArray); $x++) {
            $this->assertSame($expectedArray[$x], $tag->getArgs()[$x]);
        }

    }

    public function providerTestParseArgs() {
        return  [ ['{% YEAR|arg1 %}'                        , 1, ['arg1']                          ]
                , ['{% YEAR|arg1|arg2|arg3 %}'              , 3, ['arg1', 'arg2', 'arg3']          ]
                , ['{% YEAR|Y-m-d %}'                       , 1, ['Y-m-d']                         ]
                , ['{% HASH|{{FIRSTNAME}} %}'               , 1, ['{{FIRSTNAME}}']                 ]
                , ['{% HASH|{{FIRSTNAME}}|{{LASTNAME}} %}'  , 2, ['{{FIRSTNAME}}', '{{LASTNAME}}'] ]
                , ['{% DATE|d %}'                           , 1, ['d']                             ]
                ];
    }

    /**
     * @param $subject
     * @param $label
     * @param $type
     * @dataProvider providerTestGetLabel
     */

    public function testGetLabel($subject, $label, $expectedClass) {
        $TagFactory = new TagFactory();
        $Tag = $TagFactory->getTag($subject);

        if($expectedClass !== false) {
            $this->assertInstanceOf($expectedClass, $Tag);
        }

        if($expectedClass === false) {
            $this->assertFalse($Tag);
        }

        if($label !== false) {
            $this->assertSame($label, $Tag->getLabel());
        }
    }

    public function providerTestGetLabel(): array
    {
        return  [ [ '{{FIRSTNAME}}'   , 'FIRSTNAME'    , 'HPHIO\Farret\TemplateTag'  ]
                , [ '{{FIRSTNAME }}'  , 'FIRSTNAME'    , 'HPHIO\Farret\TemplateTag'  ]
                , [ '{{ FIRSTNAME}}'  , 'FIRSTNAME'    , 'HPHIO\Farret\TemplateTag'  ]
                , [ '{{ FIRSTNAME }}' , 'FIRSTNAME'    , 'HPHIO\Farret\TemplateTag'  ]
                , [ '{%FIRSTNAME%}'   , 'FIRSTNAME'    , 'HPHIO\Farret\TemplateHook' ]
                , [ '{%FIRSTNAME %}'  , 'FIRSTNAME'    , 'HPHIO\Farret\TemplateHook' ]
                , [ '{% FIRSTNAME%}'  , 'FIRSTNAME'    , 'HPHIO\Farret\TemplateHook' ]
                , [ '{% FIRSTNAME %}' , 'FIRSTNAME'    , 'HPHIO\Farret\TemplateHook' ]
                , [ 'FIRSTNAME %}'    , false          , false  ] //Invalid hook
                , [ 'FIRSTNAME }}'    , false          , false  ] //Invalid tag
                ];
    }


    /**
     * @dataProvider providerTestGetSetReplacement
     */

    public function testGetSetReplacement($tag) {
        $TagFactory = new TagFactory();
        $replacement = 'JJDyALQqbtDmAPtY';
        $Tag = $TagFactory->getTag($tag);
        $Tag->setReplacement($replacement);
        $this->assertSame($Tag->getReplacement(), $replacement);
    }

    public function providerTestGetSetReplacement() {
        return  [ [ '{{FIRSTNAME}}'   ]
                , [ '{% YEAR %}'      ]
                , [ '{% YEAR|arg1 %}' ]
                ];
    }
}
