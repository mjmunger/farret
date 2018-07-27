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
     * @covers Notif::$templateDirectory
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
     * @covers Notif::loadTemplate()
     */

    public function testLoadTemplate($template, $expectedExistence) {

        $Notif = new Notif();
        $Notif->setTemplateDirectory(__DIR__ . '/emails/');
        $result = $Notif->loadTemplate($template);
        $this->assertSame($result, $expectedExistence);

        if($expectedExistence) $this->assertSame($Notif->template, file_get_contents($Notif->templateDirectory . $template . '.html'));

    }

    public function providerTestLoadTemplate() {
        return  [ [ 'reset'      , true ]
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
     * @covers Notif::getTemplateTags()
     * @covers Notif::getTags()
     */

    public function testGetTemplateTags() {
        $Notif = new Notif();
        $Notif->setTemplateDirectory(__DIR__ . '/emails/');
        $Notif->loadTemplate('reset');

        $Tags = $Notif->getTemplateTags();

        $this->assertCount(5,$Tags);
    }

    /**
     * @throws Exception
     * @covers Notif::render()
     */

    public function testRender() {

        $Notif = new Notif();
        $Notif->setTemplateDirectory(__DIR__ . '/emails/');
        $Notif->loadTemplate('reset');

        $Notif->addFart("FIRSTNAME"        , "RrVkbeG");
        $Notif->addFart("LINK"             , "FxyC");
        $Notif->addFart("ANOTHERTAG"       , "mGYMmJoUDMoxUaKzX");
        $Notif->addFart("MISSINGLEFTSPACE" , "hdUxQxBiS");
        $Notif->addFart("MISSINGRIGHTSPACE", "WKKGiHXzqLesjwooBX");

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
        return  [ [ '{{FIRSTNAME}}'   , 'FIRSTNAME', true  ]
                , [ '{{FIRSTNAME }}'  , 'FIRSTNAME', true  ]
                , [ '{{ FIRSTNAME}}'  , 'FIRSTNAME', true  ]
                , [ '{{ FIRSTNAME }}' , 'FIRSTNAME', true  ]
                , [ '{{ FIRSTNAME }'  , 'FIRSTNAME', false ]
                , [ '{ FIRSTNAME }}'  , 'FIRSTNAME', false ]
                , [ '{FIRSTNAME }}'   , 'FIRSTNAME', false ]
                , [ '{{FIRSTNAME}'    , 'FIRSTNAME', false ]
                , [ '{{ FIRSTNAME }}' , 'IRSTNAME' , false ]
                , [ '{{ FIRSTNAME }}' , 'FIRSTNAM' , false ]
                ];
    }

    /**
     * @throws Exception
     * @covers Notif::getHooks()
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

    public function testGetTagType($subject, $label, $type) {
        $Notif = new Notif();
        $this->assertSame($type, $Notif->getTagType($subject));
    }

    /**
     * @param $subject
     * @param $label
     * @param $type
     * @dataProvider providerTestGetTagLabel
     */

    public function testGetTagLabel($subject, $label, $type) {
        $Notif = new Notif();
        $this->assertSame($label, $Notif->getLabel($subject));
    }

    public function providerTestGetTagLabel() {
        return  [ [ '{{FIRSTNAME}}'   , 'FIRSTNAME'    , 'tag'  ]
                , [ '{{FIRSTNAME }}'  , 'FIRSTNAME'    , 'tag'  ]
                , [ '{{ FIRSTNAME}}'  , 'FIRSTNAME'    , 'tag'  ]
                , [ '{{ FIRSTNAME }}' , 'FIRSTNAME'    , 'tag'  ]
                , [ '{%FIRSTNAME%}'   , 'FIRSTNAME'    , 'hook' ]
                , [ '{%FIRSTNAME %}'  , 'FIRSTNAME'    , 'hook' ]
                , [ '{% FIRSTNAME%}'  , 'FIRSTNAME'    , 'hook' ]
                , [ '{% FIRSTNAME %}' , 'FIRSTNAME'    , 'hook' ]
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

    }
}