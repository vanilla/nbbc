<?php
/**
 * @author Todd Burry <todd@vanillaforums.com>
 * @copyright 2009-2016 Vanilla Forums Inc.
 * @license Proprietary
 */

namespace Nbbc\Tests;

use Nbbc\BBCode;

/**
 * Backwards compatibility testing for when new functionality is introduced.
 */
class CompatibilityTest extends \PHPUnit_Framework_TestCase {
    /**
     * Test that new templates work like old templates.
     *
     * @param string $source The BBCode to parse.
     * @param string $expected The expected output.
     * @dataProvider provideTemplateTests
     */
    public function testTemplates($source, $expected) {
        $bbcode = new BBCode();
        $bbcode->setDetectURLs(true);
        $html = $bbcode->parse($source);
        $this->assertSame($expected, $html);
    }

    /**
     * Provide tests for {@link testTemplates()}.
     *
     * @return array Returns a data provider.
     */
    public function provideTemplateTests() {
        $r = [
            'url' => ['[url=foo.com]go[/url]', '<a href="foo.com" class="bbcode_url">go</a>'],
            'quote' => ['[quote=todd]This is you!!![/quote]', <<<EOT

<div class="bbcode_quote">
<div class="bbcode_quote_head">todd wrote:</div>
<div class="bbcode_quote_body">This is you!!!</div>
</div>

EOT
],
            'wiki' => ['What [[the]] heck?', 'What <a href="/?page=the" class="bbcode_wiki">the</a> heck?'],
            'email' => ['Email todd@noreply.com', 'Email <a href="mailto:todd@noreply.com">todd@noreply.com</a>'],
        ];

        return $r;
    }
}
