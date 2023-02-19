<?php
/**
 * @namspace Tests
 * @name TagFactoryTest
 * Summary: #$END$#
 *
 * Date: 2023-02-19
 * Time: 6:10 AM
 *
 * @author Michael Munger <mj@hph.io>
 * @copyright (c) 2023 High Powered Help, Inc. All Rights Reserved.
 */

namespace Tests;

use HPHIO\Farret\TagFactory;
use PHPUnit\Framework\TestCase;

class TagFactoryTest extends TestCase
{

    /**
     * @param $tag
     * @param $expectedClass
     * @dataProvider providerTestTagFactory
     */
    public function testGetTag($tag, $expectedClass)
    {
        $TagFactory = new TagFactory();
        $TagObject = $TagFactory->getTag($tag);

        $this->assertInstanceOf($expectedClass, $TagObject);
    }

    public function providerTestTagFactory()
    {
        return  [ ['{{FIRSTNAME}}'                           , 'HPHIO\Farret\TemplateTag'  ]
                , ['{{FIRSTNAME }}'                          , 'HPHIO\Farret\TemplateTag'  ]
                , ['{{ FIRSTNAME}}'                          , 'HPHIO\Farret\TemplateTag'  ]
                , ['{{ FIRSTNAME }}'                         , 'HPHIO\Farret\TemplateTag'  ]
                , ['{{      FIRSTNAME      }}'               , 'HPHIO\Farret\TemplateTag'  ]
                , ['{%FIRSTNAME%}'                           , 'HPHIO\Farret\TemplateHook' ]
                , ['{%FIRSTNAME %}'                          , 'HPHIO\Farret\TemplateHook' ]
                , ['{% FIRSTNAME%}'                          , 'HPHIO\Farret\TemplateHook' ]
                , ['{% FIRSTNAME %}'                         , 'HPHIO\Farret\TemplateHook' ]
                , ['{%      FIRSTNAME      %}'               , 'HPHIO\Farret\TemplateHook' ]
                , [ '{% YEAR|arg1 %}'                        , 'HPHIO\Farret\TemplateHookWithArgs' ]
                , [ '{% YEAR|arg1|arg2|arg3 %}'              , 'HPHIO\Farret\TemplateHookWithArgs' ]
                , [ '{% YEAR|Y-m-d %}'                       , 'HPHIO\Farret\TemplateHookWithArgs' ]
                , [ '{% HASH|{{FIRSTNAME}} %}'               , 'HPHIO\Farret\TemplateHookWithArgs' ]
                , [ '{% HASH|{{FIRSTNAME}}|{{LASTNAME}} %}'  , 'HPHIO\Farret\TemplateHookWithArgs' ]
                , [ '{% HASH|d %}'                           , 'HPHIO\Farret\TemplateHookWithArgs' ]
                ];
    }



    /**
     * @param $tag
     * @dataProvider providerTestGetTag2
     */


    public function testGetTag2($tag) {
        $TagFactory = new TagFactory($tag);
        $Tag = $TagFactory->getTag($tag);
        $this->assertSame($tag, $Tag->getTag());
    }

    public function providerTestGetTag2() {
        return  [ ["{% YEAR %}"                ]
                , ["{% YEAR|ARG1 %}"           ]
                , ["{% YEAR|ARG1|ARG2 %}"      ]
                , ["{% YEAR|ARG1|ARG2|ARG3 %}" ]
                , ["{{YEAR}}"                  ]
                , ["{{YEAR }}"                 ]
                , ["{{ YEAR }}"                ]
                ];
    }
}
