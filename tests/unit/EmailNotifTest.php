<?php
/**
 * Unit tests for the email notif in this package.
 *
 * Date: 7/26/18
 * Time: 6:28 PM
 * @author Michael Munger <mj@hph.io>
 */

namespace HPHIO\Farret;

use PHPUnit\Framework\TestCase;
use \Exception;

class EmailNotifTest extends TestCase
{
    /**
     * @param $directory
     * @param $expectedExistence
     * @dataProvider providerTestTemplateDirectory
     * @covers HPHIO\Farret\Notif::setTemplateDirectory
     */

    public function testTemplateDirectory($directory, $expectedExistence) {

        $Notif = new Notif();
        $result = $Notif->setTemplateDirectory($directory);

        $this->assertSame($result, $expectedExistence);

        if($expectedExistence) $this->assertSame($Notif->templateDirectory, $directory);
    }

    public function providerTestTemplateDirectory() {
        $existingDirectory    = __DIR__ . '/emails/';
        $nonExistentDirectory = __DIR__ . '/idonotexist/';

        return  [ [ $existingDirectory    , true ]
                , [ $nonExistentDirectory , false ]
                ];
    }

    /**
     * @param $template
     * @param $expectedExistence
     * @dataProvider providerTestLoadTemplate
     * @throws Exception
     * @covers HPHIO\Farret\Notif::loadTemplate()
     */

    public function testLoadTemplate($setTemplateDirectory, $template, $expectedExistence, $expectException) {

        $Notif = new Notif();

        if($setTemplateDirectory) {
            $Notif->setTemplateDirectory(__DIR__ . '/emails/');
        }

        if($expectException) {
            $this->expectException('Exception');
        }

        $result = $Notif->loadTemplate($template);
        $this->assertSame($result, $expectedExistence);

        if($expectedExistence) $this->assertSame($Notif->template, file_get_contents($Notif->templateDirectory . $template . '.html'));

    }

    public function providerTestLoadTemplate() {
                   //Set the template directory? //template to load  //Expected Existence  ExpectExeption?
        return  [ [ true                       , 'reset'           , true                , false ]
                , [ false                      , 'reset'           , true                , true  ]
                ];
    }


    /**
     * @param $problemFile
     * @expectedException Exception
     * @dataProvider providerTestTemplateExceptions
     */

    public function testTemplateExceptions($problemFile) {
        $Notif = new Notif();
        $Notif->setTemplateDirectory(__DIR__ . '/emails/');
        $this->expectException('Exception');
        $result = $Notif->loadTemplate($problemFile);
    }

    public function providerTestTemplateExceptions() {
        return  [ [ 'notreadable.html']
                , [ 'idonotexist'     ]
                ];
    }

    public function testSetFromName() {
        $Notif = new Notif();
        $Notif->setFromName("AKijnpyh");
        $this->assertSame("AKijnpyh", $Notif->fromName);
    }

    /**
     * @param $email
     * @param $expectedValid
     * @dataProvider providerTestSetFromAddress
     */

    public function testSetFromAddress($email, $expectedValid) {

        $Notif = new Notif();

        if($expectedValid == false) $this->expectException('Exception');

        $Notif->setFromAddress($email);

        if($expectedValid) $this->assertSame($email, $Notif->fromAddress);

    }

    public function providerTestSetFromAddress() {
        return  [ [ 'valid@example.org', true ]
                , [ 'invalid-email'    , false]
                ];
    }

    public function testSetSubjectTemplate() {
        $Notif = new Notif();
        $Notif->setSubjectTemplate("DWSp");
        $this->assertSame("DWSp", $Notif->subjectTemplate);
    }

    public function testAddFart() {
        $Notif = new Notif();

        $Notif->addFart("FIRSTNAME" , "Michael");
        $Notif->addFart("MIDDLENAME", "James"  );
        $Notif->addFart("LASTNAME"  , "Munger" );

        $this->assertCount(3, $Notif->fartDictionary);

    }

    /**
     * @throws Exception
     * @covers HPHIO\Farret\Notif::getTemplateTags()
     * @covers HPHIO\Farret\Notif::getTags()
     */

    public function testGetTemplateTags() {
        $Notif = new Notif();
        $Notif->setTemplateDirectory(__DIR__ . '/emails/');
        $Notif->loadTemplate('reset');

        $TemplateTags = $Notif->getTemplateTags();

        $this->assertCount(5,$TemplateTags);

        unset($Notif);

        $Notif = new Notif();
        $this->expectException('Exception');
        $Notif->getTemplateTags();
    }

    /**
     * @covers HPHIO\Farret\Notif::setBody()
     * @covers HPHIO\Farret\Notif::getBody()
     */

    public function testGetSetBody() {
        $Notif = new Notif();
        $Notif->setBody('BZjPUDGSMj');
        $this->assertSame('BZjPUDGSMj', $Notif->getBody());
    }

    public function testGetSetTemplate() {
        $Notif = new Notif();
        $Notif->setTemplate('YBvptJDHFMEODln');
        $this->assertSame('YBvptJDHFMEODln', $Notif->getTemplate());
    }

    /**
     * @throws Exception
     */

    public function testGetBodyTags() {
        $Notif = new Notif();
        $Notif->setTemplateDirectory(__DIR__ . '/emails/');
        $Notif->loadTemplate('reset');

        $Notif->setBody($Notif->getTemplate());

        $BodyTags = $Notif->getBodyTags();

        $this->assertCount(5,$BodyTags);

        unset($Notif);

    }

    /**
     * @throws Exception
     * @covers HPHIO\Farret\Notif::render()
     * @covers HPHIO\Farret\Notif::doFart()
     */

    public function testRender() {

        $Notif = new Notif();
        $Notif->setTemplateDirectory(__DIR__ . '/emails/');
        $Notif->loadTemplate('reset');

        $Notif->addFart("FIRSTNAME"        , "RrVkbeG");
        $Notif->addFart("LINK"             , "FxyC");
        $Notif->addFart("ANOTHERTAG"       , "mGYMmJoUDMoxUaKzX");
        $Notif->addFart("MISSINGLEFTSPACE" , "hdUxQxBiS");
        $Notif->addFart("MISSINGRIGHTSPACE", "WKKGiHXzqLesjwooBX{{ FIRSTNAME }}");

        $Tags = $Notif->getTemplateTags();
        $this->assertCount(5,$Tags);

        $Notif->render();

        $Tags = $Notif->getBodyTags();
        $this->assertCount(0,$Tags);

        $this->assertNotFalse(strpos($Notif->body, "RrVkbeG"), "RrVkbeG was not found in the body of the resulting email");
        $this->assertNotFalse(strpos($Notif->body, "FxyC"), "FxyC was not found in the body of the resulting email");
        $this->assertNotFalse(strpos($Notif->body, "mGYMmJoUDMoxUaKzX"), "mGYMmJoUDMoxUaKzX was not found in the body of the resulting email");
        $this->assertNotFalse(strpos($Notif->body, "hdUxQxBiS"), "hdUxQxBiS was not found in the body of the resulting email");
        $this->assertNotFalse(strpos($Notif->body, "WKKGiHXzqLesjwooBX"), "WKKGiHXzqLesjwooBX was not found in the body of the resulting email");

    }

    /**
     * @param $tag
     * @param $find
     * @param $expectedMatch
     * @dataProvider providerTestMatchFind
     */

    public function testMatchFind($tag, $find, $expectedMatch) {
        $Notif = new Notif();
        $this->assertSame($expectedMatch, $Notif->matchFind($tag, $find));
    }

    public function providerTestMatchFind() {
        return  [ [ '{{FIRSTNAME}}'   , 'FIRSTNAME' , true  ]
                , [ '{{FIRSTNAME }}'  , 'FIRSTNAME' , true  ]
                , [ '{{ FIRSTNAME}}'  , 'FIRSTNAME' , true  ]
                , [ '{{ FIRSTNAME }}' , 'FIRSTNAME' , true  ]
                , [ '{{ FIRSTNAME }'  , 'FIRSTNAME' , false ]
                , [ '{ FIRSTNAME }}'  , 'FIRSTNAME' , false ]
                , [ '{FIRSTNAME }}'   , 'FIRSTNAME' , false ]
                , [ '{{FIRSTNAME}'    , 'FIRSTNAME' , false ]
                , [ 'FIRSTNAME }}'    , 'FIRSTNAME' , false ]
                , [ '{{ FIRSTNAME'    , 'FIRSTNAME' , false ]
                ];
    }

    /**
     * @throws Exception
     * @covers HPHIO\Farret\Notif::getHooks()
     */

    public function testGetHooks() {
        $Notif = new Notif();
        $Notif->setTemplateDirectory(__DIR__ . '/emails/');
        $Notif->loadTemplate('reset');

        $Tags = $Notif->getHooks($Notif->template);

        $this->assertCount(3,$Tags);
    }

    public function testAddHook() {
        $Notif = new Notif();
        $Notif->setTemplateDirectory(__DIR__ . '/emails/');
        $Notif->loadTemplate('reset');

        $hookCount = count($Notif->hooks);

        $Notif->addHook('YEAR', 'getCurrentYear');
        $hookCount++;
        $this->assertCount($hookCount, $Notif->hooks);

        $Notif->addHook('MONTH', 'getCurrentMonth');
        $hookCount++;
        $this->assertCount($hookCount, $Notif->hooks);

        $Notif->addHook('DAY', 'getCurrentDat');
        $hookCount++;
        $this->assertCount($hookCount, $Notif->hooks);
    }

    /**
     * @param $subject
     * @param $label
     * @param $type
     * @dataProvider providerTestGetTagLabel
     */

    public function testGetTagLabel($subject, $label, $expectedClass) {
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

    public function providerTestGetTagLabel() {
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
     * @throws Exception
     */

    public function testRenderHook() {
        $now = new \DateTime();

        $year = $now->format('Y');
        $month = $now->format('m');
        $day = $now->format('d');
        $ISODate = $now->format("Y-m-d");


        $Notif = new Notif();
        $Notif->setTemplateDirectory(__DIR__ . '/emails/');
        $Notif->loadTemplate('reset');
        $Notif->addFart('FIRSTNAME','michael');

        $Notif->addHook('YEAR', 'getCurrentYear');
        $Notif->addHook('MONTH', 'getCurrentMonth');
        $Notif->addHook('DAY', 'getCurrentDay');

        $this->assertSame($year,$Notif->renderHook("{% YEAR %}"));
        $this->assertSame($month,$Notif->renderHook("{% MONTH|ARG1 %}"));
        $this->assertSame($day,$Notif->renderHook("{% DAY|ARG1|arg2 %}"));
        $this->assertSame($day,$Notif->renderHook("{% DATE|d %}"));
        $this->assertSame($month,$Notif->renderHook("{% DATE|m %}"));
        $this->assertSame($year,$Notif->renderHook("{% DATE|Y %}"));
        $this->assertSame($ISODate,$Notif->renderHook("{% DATE|Y-m-d %}"));

        //Exceptions tested below.
        $this->expectException('Exception');
        $this->expectExceptionMessage('The callback you requested is not registered in the notification hooks.');
        $Notif->renderHook("{% NELTVJLWRNRRW|Y-m-d %}");

    }

    public function testHookMethodDoesNotExist() {

        $Notif = new Notif();
        $Notif->addHook('NELTVJLWRNRRW', 'nonExistentMethod');
        $this->expectException('Exception');
        $this->expectExceptionMessage('Hook method does not exist! Cannot execute nonExistentMethod.');
        $Notif->renderHook("{% NELTVJLWRNRRW|Y-m-d %}");
    }

    /**
     * @param $hook
     * @param $expectedArgCount
     * @param $expectedArgs
     * @dataProvider providerTestGetArgs
     */
    public function testGetArgs($hook, $expectedArgCount, $expectedArgs ) {
        $Notif = new Notif();
        $args = $Notif->getArgs($hook);

        $this->assertCount($expectedArgCount, $args);

        for($x = 0; $x < $expectedArgCount; $x++) {
            $this->assertSame($expectedArgs[$x], $args[$x]);
        }
    }

    public function providerTestGetArgs() {
        $single = ['KMXZMMyNYfMbDNHTAEmr'];
        $double = ['AvJyu', 'ztWXCbpngQPXZda'];
        $triple = ['sbngIRWOvcMdrofCs','SEHEOqn','LcIzEEidYlXeF'];

        $hook1 = '{% TEST|KMXZMMyNYfMbDNHTAEmr %}';
        $hook2 = '{% TEST|AvJyu|ztWXCbpngQPXZda %}';
        $hook3 = '{% TEST|sbngIRWOvcMdrofCs|SEHEOqn|LcIzEEidYlXeF %}';

        return  [ [$hook1, count($single), $single ]
                , [$hook2, count($double), $double ]
                , [$hook3, count($triple), $triple ]
                ];
    }

    /**
     * @param $tag
     * @param $expectedClass
     * @dataProvider providerTestTagFactory
     */
    public function testTagFactory($tag, $expectedClass)
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
     * @dataProvider providerTestMatchFind
     */
    public function testTemplateFart($tag, $find, $expectedMatch) {
        $expectedReplacement = 'itjazbVSgSBSsxh';

        $TagFactory = new TagFactory();
        $Tag = $TagFactory->getTag($tag);

        if($expectedMatch === false) {
            $this->assertFalse($Tag);
            return;
        }

        $dictionary = [ 'FIRSTNAME' =>  $expectedReplacement];
        $result = $Tag->fart($dictionary);

        $this->assertSame($expectedMatch, $result);

            $this->assertSame($expectedReplacement, $Tag->getReplacement());
    }

    /**
     * @param $dictionaryKey
     * @param $dictionaryValue
     * @param $expectedReplacement
     * @throws Exception
     * @dataProvider providerTestArgsWithTags
     */

    public function testArgsWithTags($dictionary, $expectedReplacement, $functionArgs) {
        $Notif = new Notif();

        foreach($dictionary as $find => $replace) {
            $Notif->addFart($find, $replace);
        }

        $args = "|" . implode("|", $functionArgs);

        $tag = sprintf("{%% HASH%s %%}", $args);

        $this->assertSame($expectedReplacement, $Notif->renderHook($tag));
    }

    public function providerTestArgsWithTags() {

        $dictionary1 = [ 'FIRSTNAME' => 'michael' ];
        $dictionary2 = [ 'FIRSTNAME' => 'michael', 'LASTNAME' => 'munger' ];
        $dictionary3 = [ 'FIRSTNAME' => 'michael', 'LASTNAME' => 'munger', 'FOOBAR' => 'vVUsBuUDulZMqJgltPFr' ];

        return  [ [ $dictionary1, '0acf4539a14b3aa27deeb4cbdf6e989f' , ['{{FIRSTNAME}}'] ]
                , [ $dictionary2, '360f28e78e52106f47e85d1b8f48a37d' , ['{{FIRSTNAME}}', '{{LASTNAME}}'] ]
                , [ $dictionary2, '662feb17e595bd48d0b6385bac2c4e70' , ['{{FIRSTNAME}}', '{{LASTNAME}}', 'ExUxvqdyFC'] ]
                , [ $dictionary3, '0ca1a7da21154f04149d05a73abb844d' , ['{{FIRSTNAME}}', '{{LASTNAME}}', 'ExUxvqdyFC', '{{FOOBAR}}' ] ]
                ];
    }


}
