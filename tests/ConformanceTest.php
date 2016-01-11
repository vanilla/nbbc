<?php
/**
 * @author Todd Burry <todd@vanillaforums.com>
 * @copyright 2009-2015 Vanilla Forums Inc.
 * @license Proprietary
 */

namespace Nbbc\Tests;

use Nbbc\BBCode;

/**
 * Contains all the tests that were part of the default NBBC test page (test_nbbc.php).
 */
class ConformanceTest extends \PHPUnit_Framework_TestCase {
    /**
     * Provide input validation test data.
     *
     * @return array Returns the test function arguments.
     */
    public function provideInputValidationTests() {
        $result = [
            [[
                'descr' => "Unknown tags like [foo] get ignored.",
                'bbcode' => "This is [foo]a tag[/foo].",
                'html' => "This is [foo]a tag[/foo].",
            ]],
            [[
                'descr' => "Broken tags like [foo get ignored.",
                'bbcode' => "This is [foo a tag.",
                'html' => "This is [foo a tag.",
            ]],
            [[
                'descr' => "Broken tags like [/foo get ignored.",
                'bbcode' => "This is [/foo a tag.",
                'html' => "This is [/foo a tag.",
            ]],
            [[
                'descr' => "Broken tags like [] get ignored.",
                'bbcode' => "This is [] a tag.",
                'html' => "This is [] a tag.",
            ]],
            [[
                'descr' => "Broken tags like [/  ] get ignored.",
                'bbcode' => "This is [/  ] a tag.",
                'html' => "This is [/  ] a tag.",
            ]],
            [[
                'descr' => "Broken tags like [/ get ignored.",
                'bbcode' => "This is [/ a tag.",
                'html' => "This is [/ a tag.",
            ]],
            [[
                'descr' => "Broken [ tags before [b]real tags[/b] don't break the real tags.",
                'bbcode' => "Broken [ tags before [b]real tags[/b] don't break the real tags.",
                'html' => "Broken [ tags before <b>real tags</b> don't break the real tags.",
            ]],
            [[
                'descr' => "Broken [tags before [b]real tags[/b] don't break the real tags.",
                'bbcode' => "Broken [tags before [b]real tags[/b] don't break the real tags.",
                'html' => "Broken [tags before <b>real tags</b> don't break the real tags.",
            ]],
            [[
                'descr' => "[i][b]Mis-ordered nesting[/i][/b] gets fixed.",
                'bbcode' => "[i][b]Mis-ordered nesting[/i][/b] gets fixed.",
                'html' => "<i><b>Mis-ordered nesting</b></i> gets fixed.",
            ]],
            [[
                'descr' => "[url=][b]Mis-ordered nesting[/url][/b] gets fixed.",
                'bbcode' => "[url=http://www.google.com][b]Mis-ordered nesting[/url][/b] gets fixed.",
                'html' => "<a href=\"http://www.google.com\" class=\"bbcode_url\"><b>Mis-ordered nesting</b></a> gets fixed.",
            ]],
            [[
                'descr' => "[i]Unended blocks are automatically ended.",
                'bbcode' => "[i]Unended blocks are automatically ended.",
                'html' => "<i>Unended blocks are automatically ended.</i>",
            ]],
            [[
                'descr' => "Unstarted blocks[/i] have their end tags ignored.",
                'bbcode' => "Unstarted blocks[/i] have their end tags ignored.",
                'html' => "Unstarted blocks[/i] have their end tags ignored.",
            ]],
            [[
                'descr' => "[b]Mismatched tags[/i] are not matched to each other.",
                'bbcode' => "[b]Mismatched tags[/i] are not matched to each other.",
                'html' => "<b>Mismatched tags[/i] are not matched to each other.</b>",
            ]],
            [[
                'descr' => "[center]Inlines and [b]blocks get[/b] nested correctly[/center].",
                'bbcode' => "[center]Inlines and [b]blocks get[/b] nested correctly[/center].",
                'html' => "\n<div class=\"bbcode_center\" style=\"text-align:center\">\nInlines and <b>blocks get</b> nested correctly\n</div>\n.",
            ]],
            [[
                'descr' => "[b]Inlines and [center]blocks get[/center] nested correctly[/b].",
                'bbcode' => "[b]Inlines and [center]blocks get[/center] nested correctly[/b].",
                'html' => "<b>Inlines and </b>\n<div class=\"bbcode_center\" style=\"text-align:center\">\nblocks get\n</div>\nnested correctly.",
            ]],
            [[
                'descr' => "BBCode is [B]case-insensitive[/b].",
                'bbcode' => "[cEnTeR][b]This[/B] is a [I]test[/i].[/CeNteR]",
                'html' => "\n<div class=\"bbcode_center\" style=\"text-align:center\">\n<b>This</b> is a <i>test</i>.\n</div>\n",
            ]],
            [[
                'descr' => "Plain text gets passed through unchanged.",
                'bbcode' => "Plain text gets passed through unchanged.  b is not a tag and i is not a tag and neither is /i and neither is (b).",
                'html' => "Plain text gets passed through unchanged.  b is not a tag and i is not a tag and neither is /i and neither is (b).",
            ]],
        ];

        return $result;
    }

    /**
     * Provide special character test data.
     *
     * @return array Returns the test function arguments.
     */
    public function provideSpecialCharacterTests() {
        $result = [
            [[
                'descr' => "& and < and > and \" get replaced with HTML-safe equivalents.",
                'bbcode' => "This <woo!> &\"yeah!\" 'sizzle'",
                'html' => "This &lt;woo!&gt; &amp;&quot;yeah!&quot; 'sizzle'",
            ]],
            [[
                'descr' => ":-) produces a smiley <img> element.",
                'bbcode' => "This is a test of the emergency broadcasting system :-)",
                'regex' => <<<'REGEX'
`This is a test of the emergency broadcasting system <img src="smileys/smile.gif" alt=":-\)" title=":-\)" class="bbcode_smiley" />`
REGEX
,
            ]],
            [[
                'descr' => "--- does *not* produce a [rule] tag.",
                'bbcode' => "This is a test of the --- emergency broadcasting system.",
                'html' => "This is a test of the --- emergency broadcasting system.",
            ]],
            [[
                'descr' => "---- does *not* produce a [rule] tag.",
                'bbcode' => "This is a test of the ---- emergency broadcasting system.",
                'html' => "This is a test of the ---- emergency broadcasting system.",
            ]],
            [[
                'descr' => "----- produces a [rule] tag.",
                'bbcode' => "This is a test of the ----- emergency broadcasting system.",
                'html' => "This is a test of the\n<hr class=\"bbcode_rule\" />\nemergency broadcasting system.",
            ]],
            [[
                'descr' => "--------- produces a [rule] tag.",
                'bbcode' => "This is a test of the --------- emergency broadcasting system.",
                'html' => "This is a test of the\n<hr class=\"bbcode_rule\" />\nemergency broadcasting system.",
            ]],
            [[
                'descr' => "[-] does *not* produce a comment.",
                'bbcode' => "This is a test of the [- emergency broadcasting] system.",
                'html' => "This is a test of the [- emergency broadcasting] system.",
            ]],
            [[
                'descr' => "[--] produces a comment.",
                'bbcode' => "This is a test of the [-- emergency broadcasting] system.",
                'html' => "This is a test of the  system.",
            ]],
            [[
                'descr' => "[----] produces a comment.",
                'bbcode' => "This is a test of the [---- emergency broadcasting] system.",
                'html' => "This is a test of the  system.",
            ]],
            [[
                'descr' => "[--] comments may contain - and [ and \" and ' characters.",
                'bbcode' => "This is a test of the [-- emergency - [ \" ' broadcasting] system.",
                'html' => "This is a test of the  system.",
            ]],
            [[
                'descr' => "[--] comments may *not* contain newlines.",
                'bbcode' => "This is a test of the [-- emergency\n\rbroadcasting] system.",
                'html' => "This is a test of the [-- emergency<br>\nbroadcasting] system.",
            ]],
            [[
                'descr' => "['] produces a comment.",
                'bbcode' => "This is a test of the ['emergency broadcasting] system.",
                'html' => "This is a test of the  system.",
            ]],
            [[
                'descr' => "['] comments may contain [ and \" and ' characters.",
                'bbcode' => "This is a test of the ['emergency [ \" ' broadcasting] system.",
                'html' => "This is a test of the  system.",
            ]],
            [[
                'descr' => "['] comments may *not* contain newlines.",
                'bbcode' => "This is a test of the [' emergency\n\rbroadcasting] system.",
                'html' => "This is a test of the [' emergency<br>\nbroadcasting] system.",
            ]],
            [[
                'descr' => "[!-- --] produces a comment.",
                'bbcode' => "This is a test of the [!-- emergency broadcasting --] system.",
                'html' => "This is a test of the  system.",
            ]],
            [[
                'descr' => "[!-- ] does *not* produce a viable comment.",
                'bbcode' => "This is a test of the [!-- emergency broadcasting ] system.",
                'html' => "This is a test of the [!-- emergency broadcasting ] system.",
            ]],
            [[
                'descr' => "[!-- - -- ] [ --] produces a comment.",
                'bbcode' => "This is a test of the [!-- emergency - broadcasting -- system ] thingy --].",
                'html' => "This is a test of the .",
            ]],
            [[
                'descr' => "[!-- - -- ] [ --] --] produces a comment with a --] left over.",
                'bbcode' => "This is a test of the [!-- emergency - broadcasting -- system ] thingy --] and other --] stuff.",
                'html' => "This is a test of the  and other --] stuff.",
            ]],
            [[
                'descr' => "[!-- --] does not break any following tags outside it.",
                'bbcode' => "The [!-- quick brown --]fox jumps over the [b]lazy[/b] [i]dog[/i].",
                'html' => "The fox jumps over the <b>lazy</b> <i>dog</i>.",
            ]],
            [[
                'descr' => "Tag marker mode '<' works correctly.",
                'bbcode' => "This is <b>a <i>test</b></i>.",
                'html' => "This is <b>a <i>test</i></b>.",
                'tag_marker' => '<',
            ]],
            [[
                'descr' => "Tag marker mode '{' works correctly.",
                'bbcode' => "This is {b}a {i}test{/b}{/i}.",
                'html' => "This is <b>a <i>test</i></b>.",
                'tag_marker' => '{',
            ]],
            [[
                'descr' => "Tag marker mode '(' works correctly.",
                'bbcode' => "This is (b)a (i)test(/b)(/i).",
                'html' => "This is <b>a <i>test</i></b>.",
                'tag_marker' => '(',
            ]],
            [[
                'descr' => "Ampersand pass-through mode works correctly.",
                'bbcode' => "This is <b>a <i>test</b></i> &amp; some junk.",
                'html' => "This is <b>a <i>test</i></b> &amp; some junk.",
                'tag_marker' => '<',
            ]],
        ];
        return $result;
    }

    /**
     * Provide white space test data.
     *
     * @return array Returns the test function arguments.
     */
    public function provideWhitespaceTests() {
        $result = [
            [[
                'descr' => "Newlines get replaced with <br> tags.",
                'bbcode' => "This\nis\r\na\n\rtest.",
                'html' => "This<br>\nis<br>\na<br>\ntest.",
            ]],
            [[
                'descr' => "Newlines *don't* get replaced with <br> tags in ignore-newline mode.",
                'bbcode' => "This\nis\r\na\n\rtest.",
                'html' => "This\nis\na\ntest.",
                'newline_ignore' => true,
            ]],
            [[
                'descr' => "Space before and after newlines gets removed.",
                'bbcode' => "This \n \t is \na\n \x08test.",
                'html' => "This<br>\nis<br>\na<br>\ntest.",
            ]],
            [[
                'descr' => "Whitespace doesn't matter inside tags after the tag name.",
                'bbcode' => "This [size = 4  ]is a test[/size ].",
                'html' => "This <span style=\"font-size:1.17em\">is a test</span>.",
            ]],
            [[
                'descr' => "Whitespace does matter inside \"quotes\" in tags.",
                'bbcode' => "This [wstest=\"  Courier   New  \"]is a test[/wstest].",
                'html' => "This <span style=\"wstest:  Courier   New  \">is a test</span>.",
            ]],
            [[
                'descr' => "Whitespace does matter inside 'quotes' in tags.",
                'bbcode' => "This [wstest='  Courier   New  ']is a test[/wstest].",
                'html' => "This <span style=\"wstest:  Courier   New  \">is a test</span>.",
            ]],
            [[
                'descr' => "Whitespace is properly collapsed near block tags like [center].",
                'bbcode' => <<<BBCODE
Not centered.

[center]

    A bold stone gathers no italics.

[/center]

Not centered.
BBCODE
,
                'html' => "Not centered.<br>\n"
                    . "\n<div class=\"bbcode_center\" style=\"text-align:center\">\n"
                    . "<br>\n"
                    . "A bold stone gathers no italics.<br>\n"
                    . "\n</div>\n"
                    . "<br>\n"
                    . "Not centered.",
            ]],
            [[
                'descr' => "[code]...[/code] should strip whitespace outside it but not inside it.",
                'bbcode' => "Not\ncode.\n"
                    . "[code]    \n\n    This is a test.    \n\n    [/code]\n"
                    . "Also not code.\n",
                'html' => "Not<br>\ncode.\n"
                    . "<div class=\"bbcode_code\">\n"
                    . "<div class=\"bbcode_code_head\">Code:</div>\n"
                    . "<div class=\"bbcode_code_body\" style=\"white-space:pre\">\n    This is a test.    \n</div>\n"
                    . "</div>\n"
                    . "Also not code.<br>\n",
            ]],
            [[
                'descr' => "[list] and [*] must consume correct quantities of whitespace.",
                'bbcode' => "[list]\n\n\t[*] One Box\n\n\t[*] Two Boxes\n\t[*] \n Three Boxes\n\n[/list]\n",
                'html' => "\n<ul class=\"bbcode_list\">\n<br>\n<li>One Box<br>\n</li>\n<li>Two Boxes</li>\n<li><br>\nThree Boxes<br>\n</li>\n</ul>\n",
            ]],
        ];
        return $result;
    }

    /**
     * Provide inline tag conversion test data.
     *
     * @return array Returns the test function arguments.
     */
    public function provideInlineConversionTests() {
        $result = [
            [[
                'descr' => "[i] gets correctly converted.",
                'bbcode' => "This is a test of the [i]emergency broadcasting system[/i].",
                'html' => "This is a test of the <i>emergency broadcasting system</i>.",
            ]],
            [[
                'descr' => "[b] gets correctly converted.",
                'bbcode' => "This is a test of the [b]emergency broadcasting system[/b].",
                'html' => "This is a test of the <b>emergency broadcasting system</b>.",
            ]],
            [[
                'descr' => "[u] gets correctly converted.",
                'bbcode' => "This is a test of the [u]emergency broadcasting system[/u].",
                'html' => "This is a test of the <u>emergency broadcasting system</u>.",
            ]],
            [[
                'descr' => "[s] gets correctly converted.",
                'bbcode' => "This is a test of the [s]emergency broadcasting system[/s].",
                'html' => "This is a test of the <strike>emergency broadcasting system</strike>.",
            ]],
            [[
                'descr' => "[sup] gets correctly converted.",
                'bbcode' => "This is a test of the [sup]emergency broadcasting system[/sup].",
                'html' => "This is a test of the <sup>emergency broadcasting system</sup>.",
            ]],
            [[
                'descr' => "[sub] gets correctly converted.",
                'bbcode' => "This is a test of the [sub]emergency broadcasting system[/sub].",
                'html' => "This is a test of the <sub>emergency broadcasting system</sub>.",
            ]],
            [[
                'descr' => "[font=Arial] gets correctly converted (simple font name).",
                'bbcode' => "This is a test of the [font=Arial]emergency broadcasting system[/font].",
                'html' => "This is a test of the <span style=\"font-family:'Arial'\">emergency broadcasting system</span>.",
            ]],
            [[
                'descr' => "[font=Times New Roman] gets correctly converted (unquoted default value).",
                'bbcode' => "This is a test of the [font=Times New Roman]emergency broadcasting system[/font].",
                'html' => "This is a test of the <span style=\"font-family:'Times New Roman'\">emergency broadcasting system</span>.",
            ]],
            [[
                'descr' => "[font=Times New Roman size=1] gets converted (trailing parameter identified).",
                'bbcode' => "This is a test of the [font=Times New Roman size=1]emergency broadcasting system[/font].",
                'html' => "This is a test of the <span style=\"font-family:'Times New Roman'\">emergency broadcasting system</span>.",
            ]],
            [[
                'descr' => "[font=\"Courier New\"] gets correctly converted (quoted default value).",
                'bbcode' => "This is a test of the [font=\"Courier New\"]emergency broadcasting system[/font].",
                'html' => "This is a test of the <span style=\"font-family:'Courier New'\">emergency broadcasting system</span>.",
            ]],
            [[
                'descr' => "[font=\"Courier New\" blarg size=1] gets converted (floating parameter ignored).",
                'bbcode' => "This is a test of the [font=\"Courier New\" blarg size=1]emergency broadcasting system[/font].",
                'html' => "This is a test of the <span style=\"font-family:'Courier New'\">emergency broadcasting system</span>.",
            ]],
            [[
                'descr' => "[size=6] gets correctly converted.",
                'bbcode' => "This is a test of the [size=6]emergency broadcasting system[/size].",
                'html' => "This is a test of the <span style=\"font-size:2.0em\">emergency broadcasting system</span>.",
            ]],
            [[
                'descr' => "[size=10] gets correctly converted.",
                'bbcode' => "This is a test of the [size=10]emergency broadcasting system[/size].",
                'html' => "This is a test of the <span style=\"font-size:1.0em\">emergency broadcasting system</span>.",
            ]],
            [[
                'descr' => "[size=blah] gets ignored.",
                'bbcode' => "This is a test of the [size=blah]emergency broadcasting system[/size].",
                'html' => "This is a test of the [size=blah]emergency broadcasting system[/size].",
            ]],
            [[
                'descr' => "[color=red] gets correctly converted.",
                'bbcode' => "This is a test of the [color=red]emergency broadcasting system[/color].",
                'html' => "This is a test of the <span style=\"color:red\">emergency broadcasting system</span>.",
            ]],
            [[
                'descr' => "[color=gronk] gets correctly converted.",
                'bbcode' => "This is a test of the [color=gronk]emergency broadcasting system[/color].",
                'html' => "This is a test of the <span style=\"color:gronk\">emergency broadcasting system</span>.",
            ]],
            [[
                'descr' => "[color=#FFF] gets correctly converted.",
                'bbcode' => "This is a test of the [color=#FFF]emergency broadcasting system[/color].",
                'html' => "This is a test of the <span style=\"color:#FFF\">emergency broadcasting system</span>.",
            ]],
            [[
                'descr' => "[color=*#\$] is prohibited.",
                'bbcode' => "This is a test of the [color=*#\$]emergency broadcasting system[/color].",
                'html' => "This is a test of the [color=*#\$]emergency broadcasting system[/color].",
            ]],
            [[
                'descr' => "[spoiler] gets converted.",
                'bbcode' => "Ssh, don't tell, but [spoiler]Darth is Luke's father[/spoiler]!",
                'html' => "Ssh, don't tell, but <span class=\"bbcode_spoiler\">Darth is Luke's father</span>!",
            ]],
            [[
                'descr' => "[acronym] gets converted.",
                'bbcode' => "The [acronym=\"British Broadcasting Company\"]BBC[/acronym] airs [i]Doctor Who[/i] on Saturdays.",
                'html' => "The <span class=\"bbcode_acronym\" title=\"British Broadcasting Company\">BBC</span> airs <i>Doctor Who</i> on Saturdays.",
            ]],
            [[
                'descr' => "[acronym] safely encodes its content.",
                'bbcode' => "The [acronym=_\"><script>alert(/XSS/.source)</script><x]Foo[/acronym] is safe.",
                'html' => "The <span class=\"bbcode_acronym\" title=\"_&quot;&gt;&lt;script&gt;alert(/XSS/.source)&lt;/script&gt;&lt;x\">Foo</span> is safe.",
            ]],
        ];
        return $result;
    }

    /**
     * Provide **[url]** tag test data.
     *
     * @return array Returns the test function arguments.
     */
    public function provideUrlTests() {
        $result = [
            [[
                'descr' => "[url=...] (with no protocol given) gets converted.",
                'bbcode' => "This is a test of the [url=fleeb.html]emergency broadcasting system[/url].",
                'html' => "This is a test of the <a href=\"fleeb.html\" class=\"bbcode_url\">emergency broadcasting system</a>.",
            ]],
            [[
                'descr' => "[url=http:...] gets converted.",
                'bbcode' => "This is a test of the [url=http://www.google.com]emergency broadcasting system[/url].",
                'html' => "This is a test of the <a href=\"http://www.google.com\" class=\"bbcode_url\">emergency broadcasting system</a>.",
            ]],
            [[
                'descr' => "[url=http:...] gets converted correctly in plain mode.",
                'bbcode' => "This is a test of the [url=http://www.google.com]emergency broadcasting system[/url].",
                'html' => "This is a test of the <a href=\"http://www.google.com\">emergency broadcasting system</a>.",
                'plainmode' => true,
            ]],
            [[
                'descr' => "Unquoted [url=http:...] with parameters gets converted.",
                'bbcode' => "This is a test of the [url=http://www.google.com?q=broadcasting&y=foo&x=bar]emergency broadcasting system[/url].",
                'html' => "This is a test of the <a href=\"http://www.google.com?q=broadcasting&amp;y=foo&amp;x=bar\" class=\"bbcode_url\">emergency broadcasting system</a>.",
            ]],
            [[
                'descr' => "[url=https:...] gets converted.",
                'bbcode' => "This is a test of the [url=https://www.google.com]emergency broadcasting system[/url].",
                'html' => "This is a test of the <a href=\"https://www.google.com\" class=\"bbcode_url\">emergency broadcasting system</a>.",
            ]],
            [[
                'descr' => "[url=ftp:...] gets converted.",
                'bbcode' => "This is a test of the [url=ftp://www.google.com]emergency broadcasting system[/url].",
                'html' => "This is a test of the <a href=\"ftp://www.google.com\" class=\"bbcode_url\">emergency broadcasting system</a>.",
            ]],
            [[
                'descr' => "[url=mailto:...] gets converted.",
                'bbcode' => "This is a test of the [url=mailto:john@example.com]emergency broadcasting system[/url].",
                'html' => "This is a test of the <a href=\"mailto:john@example.com\" class=\"bbcode_url\">emergency broadcasting system</a>.",
            ]],
            [[
                'descr' => "[url=javascript:...] is prohibited.",
                'bbcode' => "This is a test of the [url=javascript:alert()]emergency broadcasting system[/url].",
                'html' => "This is a test of the [url=javascript:alert()]emergency broadcasting system[/url].",
            ]],
            [[
                'descr' => "[url=(unknown protocol):...] is prohibited.",
                'bbcode' => "This is a test of the [url=flooble:blarble]emergency broadcasting system[/url].",
                'html' => "This is a test of the [url=flooble:blarble]emergency broadcasting system[/url].",
            ]],
            [[
                'descr' => "The [url]http://...[/url] form works correctly.",
                'bbcode' => "The [url]http://www.google.com[/url] form works correctly.",
                'html' => "The <a href=\"http://www.google.com\" class=\"bbcode_url\">http://www.google.com</a> form works correctly.",
            ]],
            [[
                'descr' => "The [url]http://...[/url] form works correctly in plain mode.",
                'bbcode' => "The [url]http://www.google.com[/url] form works correctly.",
                'html' => "The <a href=\"http://www.google.com\">http://www.google.com</a> form works correctly.",
                'plainmode' => true,
            ]],
            [[
                'descr' => "The [url]malformed...url...[/url] form is fully unprocessed.",
                'bbcode' => "The [url]a.imagehost.org/view/egdgdo[/url] form is fully unprocessed.",
                'html' => "The <a href=\"a.imagehost.org/view/egdgdo\" class=\"bbcode_url\">a.imagehost.org/view/egdgdo</a> form is fully unprocessed.",
            ]],
            [[
                'descr' => "[url=\"...=...\"] contains an embedded equal sign (quotes work correctly).",
                'bbcode' => "The [url=\"http://www.google.com/?foo=bar&baz=frob\" bar=foo]link[/url] works correctly.",
                'html' => "The <a href=\"http://www.google.com/?foo=bar&amp;baz=frob\" class=\"bbcode_url\">link</a> works correctly.",
            ]],
            [[
                'descr' => "[url=\"...=...\"] contains an embedded equal sign (test #2).",
                'bbcode' => "The [url=\"http://www.demourl.com/opinion.php?idopinion=234\"]Opinion[/url] is funny.",
                'html' => "The <a href=\"http://www.demourl.com/opinion.php?idopinion=234\" class=\"bbcode_url\">Opinion</a> is funny.",
            ]],
            [[
                'descr' => "[url=\"...\" target=\"...\"] has its target ignored by default.",
                'bbcode' => "The [url=\"http://www.demourl.com/opinion.php?idopinion=234\" target=_blank]Opinion[/url] is funny.",
                'html' => "The <a href=\"http://www.demourl.com/opinion.php?idopinion=234\" class=\"bbcode_url\">Opinion</a> is funny.",
            ]],
            [[
                'descr' => "[url=\"...\" target=\"...\"] has its target used when URL targeting is enabled.",
                'bbcode' => "The [url=\"http://www.demourl.com/opinion.php?idopinion=234\" target=_blank]Opinion[/url] is funny.",
                'html' => "The <a href=\"http://www.demourl.com/opinion.php?idopinion=234\" class=\"bbcode_url\" target=\"_blank\">Opinion</a> is funny.",
                'urltarget' => true,
            ]],
            [[
                'descr' => "[url] has a target applied when forced URL targeting is enabled.",
                'bbcode' => "The [url=\"http://www.demourl.com/opinion.php?idopinion=234\"]Opinion[/url] is funny.",
                'html' => "The <a href=\"http://www.demourl.com/opinion.php?idopinion=234\" class=\"bbcode_url\" target=\"somewhere\">Opinion</a> is funny.",
                'urlforcetarget' => "somewhere",
            ]],
            [[
                'descr' => "[url target=\"...\"] has its target ignored when forced URL targeting is enabled.",
                'bbcode' => "The [url=\"http://www.demourl.com/opinion.php?idopinion=234\" target=\"_blank\"]Opinion[/url] is funny.",
                'html' => "The <a href=\"http://www.demourl.com/opinion.php?idopinion=234\" class=\"bbcode_url\" target=\"somewhere\">Opinion</a> is funny.",
                'urlforcetarget' => "somewhere",
            ]],
            [[
                'descr' => "[url] has a target applied even with URL target overriding.",
                'bbcode' => "The [url=\"http://www.demourl.com/opinion.php?idopinion=234\"]Opinion[/url] is funny.",
                'html' => "The <a href=\"http://www.demourl.com/opinion.php?idopinion=234\" class=\"bbcode_url\" target=\"somewhere\">Opinion</a> is funny.",
                'urlforcetarget' => "somewhere",
                'urltarget' => 'override',
            ]],
            [[
                'descr' => "[url target=\"...\"] has its target applied with URL target overriding.",
                'bbcode' => "The [url=\"http://www.demourl.com/opinion.php?idopinion=234\" target=\"_blank\"]Opinion[/url] is funny.",
                'html' => "The <a href=\"http://www.demourl.com/opinion.php?idopinion=234\" class=\"bbcode_url\" target=\"_blank\">Opinion</a> is funny.",
                'urlforcetarget' => "somewhere",
                'urltarget' => 'override',
            ]],
            [[
                'descr' => "[url=(includes a smiley)] is not converted into a smiley.",
                'bbcode' => "This is a test of the [url=http://www.google.com/foo:-P]emergency broadcasting system[/url].",
                'html' => "This is a test of the <a href=\"http://www.google.com/foo:-P\" class=\"bbcode_url\">emergency broadcasting system</a>.",
            ]],
        ];
        return $result;
    }

    /**
     * Provide auto-embedded URL test data.
     *
     * @return array Returns the test function arguments.
     */
    public function provideEmbeddedUrlTests() {
        $result = [
            [[
                'descr' => "Embedded URLs get detected and converted.",
                'bbcode' => "Go to http://www.google.com for your search needs!",
                'html' => "Go to <a href=\"http://www.google.com\">http://www.google.com</a> for your search needs!",
                'detect_urls' => true,
            ]],
            [[
                'descr' => "Embedded HTTPS URLs get detected and converted.",
                'bbcode' => "Go to https://www.google.com for your search needs!",
                'html' => "Go to <a href=\"https://www.google.com\">https://www.google.com</a> for your search needs!",
                'detect_urls' => true,
            ]],
            [[
                'descr' => "Embedded FTP URLs get detected and converted.",
                'bbcode' => "Go to ftp://www.google.com for your search needs!",
                'html' => "Go to <a href=\"ftp://www.google.com\">ftp://www.google.com</a> for your search needs!",
                'detect_urls' => true,
            ]],
            [[
                'descr' => "Embedded Javascript URLs are properly ignored.",
                'bbcode' => "Go to javascript:foo.com;alert(); for your search needs!",
                'html' => "Go to javascript:foo.com;alert(); for your search needs!",
                'detect_urls' => true,
            ]],
            [[
                'descr' => "Embedded domain names get detected and converted.",
                'bbcode' => "Go to www.google.com for your search needs!",
                'html' => "Go to <a href=\"http://www.google.com\">www.google.com</a> for your search needs!",
                'detect_urls' => true,
            ]],
            [[
                'descr' => "Embedded IPs get detected and converted.",
                'bbcode' => "Go to 127.0.0.1:667/flarb for your own computer!",
                'html' => "Go to <a href=\"http://127.0.0.1:667/flarb\">127.0.0.1:667/flarb</a> for your own computer!",
                'detect_urls' => true,
            ]],
            [[
                'descr' => "Embedded addresses are smart about being inside parentheses.",
                'bbcode' => "I love Google! (google.com)",
                'html' => "I love Google! (<a href=\"http://google.com\">google.com</a>)",
                'detect_urls' => true,
            ]],
            [[
                'descr' => "Embedded-URL detector disallows junk that only seems like a URL.",
                'bbcode' => "I browse alt.net.screw-you:80/flarb all the time.",
                'html' => "I browse alt.net.screw-you:80/flarb all the time.",
                'detect_urls' => true,
            ]],
            [[
                'descr' => "Embedded-URL detector also detects e-mail addresses.",
                'bbcode' => "Send complaints to complaints@whitehouse.gov .",
                'html' => "Send complaints to <a href=\"mailto:complaints@whitehouse.gov\">complaints@whitehouse.gov</a> .",
                'detect_urls' => true,
            ]],
            [[
                'descr' => "Embedded-URL detector takes precedence over the smiley detector.",
                'bbcode' => "This is a good dictionary:  http://www.amazon.com/Oxford-Dictionary-American-Usage-Style/dp/0195135083/ref=pd_bbs_sr_1?ie=UTF8&s=books&qid=1217890161&sr=8-1&x=p",
                'html' => "This is a good dictionary:  <a href=\"http://www.amazon.com/Oxford-Dictionary-American-Usage-Style/dp/0195135083/ref=pd_bbs_sr_1?ie=UTF8&amp;s=books&amp;qid=1217890161&amp;sr=8-1&amp;x=p\">http://www.amazon.com/Oxford-Dictionary-American-Usage-Style/dp/0195135083/ref=pd_bbs_sr_1?ie=UTF8&amp;s=books&amp;qid=1217890161&amp;sr=8-1&amp;x=p</a>",
                'detect_urls' => true,
            ]],
            [[
                'descr' => "Embedded-URL detector handles single subdomain names.",
                'bbcode' => "m.example.com is a mobile site.",
                'html' => "<a href=\"http://m.example.com\">m.example.com</a> is a mobile site.",
                'detect_urls' => true,
            ]],
            [[
                'descr' => "Embedded domain names at the end of sentences get detected and converted.",
                'bbcode' => "Go to www.google.com.",
                'html' => "Go to <a href=\"http://www.google.com\">www.google.com</a>.",
                'detect_urls' => true,
            ]],
            [[
                'descr' => "Embedded links to an invalid TLD with a scheme should work.",
                'bbcode' => 'Go to http://foo.bar',
                'html' => 'Go to <a href="http://foo.bar">http://foo.bar</a>',
                'detect_urls' => true
            ]],
            [[
                'descr' => "Embedded links to an invalid TLD without a scheme should not work.",
                'bbcode' => 'Go to foo.bar',
                'html' => 'Go to foo.bar',
                'detect_urls' => true
            ]],
        ];

        return $result;
    }

    /**
     * Provide test data for URL-like tags.
     *
     * These tags include **[email]** and **[[wiki]]** links.
     *
     * @return array Returns the test function arguments.
     */
    public function provideUrlLikeTagTests() {
        $result = [
            [[
                'descr' => "[email] gets converted.",
                'bbcode' => "Send complaints to [email]john@example.com[/email].",
                'html' => "Send complaints to <a href=\"mailto:john@example.com\" class=\"bbcode_email\">john@example.com</a>.",
            ]],
            [[
                'descr' => "[email] supports both forms.",
                'bbcode' => "Send complaints to [email=john@example.com]John Smith[/email].",
                'html' => "Send complaints to <a href=\"mailto:john@example.com\" class=\"bbcode_email\">John Smith</a>.",
            ]],
            [[
                'descr' => "Bad addresses in [email] are ignored.",
                'bbcode' => "Send complaints to [email]jo\"hn@@@exa:mple.com[/email].",
                'html' => "Send complaints to [email]jo&quot;hn@@@exa:mple.com[/email].",
            ]],
            /*
                    [[
                        'descr' => "[video=youtube] gets converted.",
                        'bbcode' => "Watch this cute doggy!!! [video=youtube]dQw4w9WgXcQ[/video]",
                        'html' => "Watch this cute doggy!!! <object width=\"480\" height=\"385\"><param name=\"movie\" value=\"http://www.youtube.com/v/dQw4w9WgXcQ&hl=en_US&fs=1&\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=\"http://www.youtube.com/v/dQw4w9WgXcQ&hl=en_US&fs=1&\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"480\" height=\"385\"></embed></object>",
                    ]],
                    [[
                        'descr' => "[video=hulu] gets converted.",
                        'bbcode' => "Gleeks: [video=hulu]yuo37ilvL7pUlsKJmA6R0g[/video]",
                        'html' => "Gleeks: <object width=\"512\" height=\"288\"><param name=\"movie\" value=\"http://www.hulu.com/embed/yuo37ilvL7pUlsKJmA6R0g\"></param><param name=\"allowFullScreen\" value=\"true\"></param><embed src=\"http://www.hulu.com/embed/yuo37ilvL7pUlsKJmA6R0g\" type=\"application/x-shockwave-flash\"  width=\"512\" height=\"288\" allowFullScreen=\"true\"></embed></object>",
                    ]],
                    [[
                        'descr' => "[video] ignores unknown video services.",
                        'bbcode' => "Watch this cute doggy!!! [video=flarb]abcdefg[/video]",
                        'html' => "Watch this cute doggy!!! [video=flarb]abcdefg[/video]",
                    ]],
                    [[
                        'descr' => "[video] ignores bad video IDs.",
                        'bbcode' => "Watch this cute doggy!!! [video=youtube]b!:=9_?[/video]",
                        'html' => "Watch this cute doggy!!! [video=youtube]b!:=9_?[/video]",
                    ]],
                    [[
                        'descr' => "[video] correctly supports width= and height= modifiers.",
                        'bbcode' => "Watch this cute doggy!!! [video=youtube width=320 height=240]dQw4w9WgXcQ[/video]",
                        'html' => "Watch this cute doggy!!! <object width=\"320\" height=\"240\"><param name=\"movie\" value=\"http://www.youtube.com/v/dQw4w9WgXcQ&hl=en_US&fs=1&\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=\"http://www.youtube.com/v/dQw4w9WgXcQ&hl=en_US&fs=1&\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"320\" height=\"240\"></embed></object>",
                    ]],
            */
            [[
                'descr' => "The [[wiki]] special tag produces a wiki link.",
                'bbcode' => "This is a test of the [[wiki]] tag.",
                'html' => "This is a test of the <a href=\"/?page=wiki\" class=\"bbcode_wiki\">wiki</a> tag.",
            ]],
            [[
                'descr' => "The [[wiki]] special tag does not convert [a-zA-Z0-9'\".:_-].",
                'bbcode' => "This is a test of the [[\"Ab1cd'Ef2gh_Ij3kl.,Mn4op:Qr9st-Uv0wx\"]] tag.",
                'html' => "This is a test of the <a href=\"/?page=%22Ab1cd%27Ef2gh_Ij3kl.%2CMn4op%3AQr9st_Uv0wx%22\" class=\"bbcode_wiki\">&quot;Ab1cd'Ef2gh_Ij3kl.,Mn4op:Qr9st-Uv0wx&quot;</a> tag.",
            ]],
            [[
                'descr' => "The [[wiki]] special tag can contain spaces.",
                'bbcode' => "This is a test of the [[northwestern salmon]].",
                'html' => "This is a test of the <a href=\"/?page=northwestern_salmon\" class=\"bbcode_wiki\">northwestern salmon</a>.",
            ]],
            [[
                'descr' => "The [[wiki]] special tag cannot contain newlines.",
                'bbcode' => "This is a test of the [[northwestern\nsalmon]].",
                'html' => "This is a test of the [[northwestern<br>\nsalmon]].",
            ]],
            [[
                'descr' => "The [[wiki]] special tag can contain a title after a | character.",
                'bbcode' => "This is a test of the [[northwestern salmon|Northwestern salmon are yummy!]].",
                'html' => "This is a test of the <a href=\"/?page=northwestern_salmon\" class=\"bbcode_wiki\">Northwestern salmon are yummy!</a>.",
            ]],
            [[
                'descr' => "The [[wiki]] special tag doesn't damage anything outside it.",
                'bbcode' => "I really loved reading [[arc 1|the first story arc]] because it was more entertaining than [[arc 2|the second story arc]] was.",
                'html' => "I really loved reading <a href=\"/?page=arc_1\" class=\"bbcode_wiki\">the first story arc</a> because it was more entertaining than <a href=\"/?page=arc_2\" class=\"bbcode_wiki\">the second story arc</a> was.",
            ]],
            [[
                'descr' => "The [[wiki]] special tag condenses and trims internal whitespace.",
                'bbcode' => "This is a test of the [[  northwestern \t salmon   |   Northwestern   salmon are   yummy!  ]].",
                'html' => "This is a test of the <a href=\"/?page=northwestern_salmon\" class=\"bbcode_wiki\">Northwestern   salmon are   yummy!</a>.",
            ]],
        ];
        return $result;
    }

    /**
     * Provide **[img]** tag test data.
     *
     * @return array Returns the test function arguments.
     */
    public function provideImageTests() {
        $result = [
            [[
                'descr' => "[img] produces an image.",
                'bbcode' => "This is Google's logo: [img]http://www.google.com/intl/en_ALL/images/logo.gif[/img].",
                'html' => "This is Google's logo: <img src=\"http://www.google.com/intl/en_ALL/images/logo.gif\" alt=\"logo.gif\" class=\"bbcode_img\" />.",
            ]],
            [[
                'descr' => "[img] disallows a javascript: URL.",
                'bbcode' => "This is Google's logo: [img]javascript:alert()[/img].",
                'html' => "This is Google's logo: [img]javascript:alert()[/img].",
            ]],
            [[
                'descr' => "[img] disallows a URL with an unknown protocol type.",
                'bbcode' => "This is Google's logo: [img]foobar:bar.jpg[/img].",
                'html' => "This is Google's logo: [img]foobar:bar.jpg[/img].",
            ]],
            [[
                'descr' => "[img] disallows HTML content.",
                'bbcode' => "This is Google's logo: [img]<a href='javascript:alert(\"foo\")'>click me</a>[/img].",
                'html' => "This is Google's logo: [img]&lt;a href='javascript:alert(&quot;foo&quot;)'&gt;click me&lt;/a&gt;[/img].",
            ]],
            [[
                'descr' => "[img] can produce a local image.",
                'bbcode' => "This is a smiley: [img]smile.gif[/img].",
                'html' => "This is a smiley: <img src=\"smileys/smile.gif\" alt=\"smile.gif\" class=\"bbcode_img\" />.",
            ]],
            [[
                'descr' => "[img] can produce a local rooted URL.",
                'bbcode' => "This is a smiley: [img]/smile.gif[/img].",
                'html' => "This is a smiley: <img src=\"/smile.gif\" alt=\"smile.gif\" class=\"bbcode_img\" />.",
            ]],
            [[
                'descr' => "[img] can produce a local relative URL.",
                'bbcode' => "This is a smiley: [img]../smile.gif[/img].",
                'html' => "This is a smiley: <img src=\"../smile.gif\" alt=\"smile.gif\" class=\"bbcode_img\" />.",
            ]],
            [[
                'descr' => "[img=src] should produce an image.",
                'bbcode' => 'This is a smiley: [img=smile.gif?f=1&b=2][/img] okay?',
                'html' => 'This is a smiley: <img src="smileys/smile.gif" alt="smile.gif?f=1&amp;b=2" class="bbcode_img" /> okay?',
            ]]
        ];
        return $result;
    }

    /**
     * Provide block tag conversion test data.
     *
     * @return array Returns the test function arguments.
     */
    public function provideBlockTagConversionTests() {
        $result = [
            [[
                'descr' => "[rule] produces a horizontal rule.",
                'bbcode' => "This is a test of the [rule] emergency broadcasting system.",
                'html' => "This is a test of the\n<hr class=\"bbcode_rule\" />\nemergency broadcasting system.",
            ]],
            [[
                'descr' => "[br] is equivalent to a newline.",
                'bbcode' => "This is a newline.    [br]    And here we are!    \n  And more!",
                'html' => "This is a newline.<br>\nAnd here we are!<br>\nAnd more!",
            ]],
            [[
                'descr' => "[center]...[/center] should produce centered alignment.",
                'bbcode' => "Not centered.[center]A [b]bold[/b] stone gathers no italics.[/center]Not centered.",
                'html' => "Not centered.\n"
                    . "<div class=\"bbcode_center\" style=\"text-align:center\">\n"
                    . "A <b>bold</b> stone gathers no italics.\n"
                    . "</div>\n"
                    . "Not centered.",
            ]],
            [[
                'descr' => "[left]...[/left] should produce left alignment.",
                'bbcode' => "Not left.[left]A [b]bold[/b] stone gathers no italics.[/left]Not left.",
                'html' => "Not left.\n"
                    . "<div class=\"bbcode_left\" style=\"text-align:left\">\n"
                    . "A <b>bold</b> stone gathers no italics.\n"
                    . "</div>\n"
                    . "Not left.",
            ]],
            [[
                'descr' => "[right]...[/right] should produce right alignment.",
                'bbcode' => "Not right.[right]A [b]bold[/b] stone gathers no italics.[/right]Not right.",
                'html' => "Not right.\n"
                    . "<div class=\"bbcode_right\" style=\"text-align:right\">\n"
                    . "A <b>bold</b> stone gathers no italics.\n"
                    . "</div>\n"
                    . "Not right.",
            ]],
            [[
                'descr' => "[indent]...[/indent] should produce indented content.",
                'bbcode' => "Not indented.[indent]A [b]bold[/b] stone gathers no italics.[/indent]Not indented.",
                'html' => "Not indented.\n"
                    . "<div class=\"bbcode_indent\" style=\"margin-left:4em\">\n"
                    . "A <b>bold</b> stone gathers no italics.\n"
                    . "</div>\n"
                    . "Not indented.",
            ]],
            [[
                'descr' => "[code]...[/code] should reproduce its contents exactly as they're given.",
                'bbcode' => "Not code."
                    . "[code]A [b]and[/b] & <woo>!\n\tAnd a ['hey'] and a [/nonny] and a ho ho ho![/code]"
                    . "Also not code.",
                'html' => "Not code."
                    . "\n<div class=\"bbcode_code\">\n"
                    . "<div class=\"bbcode_code_head\">Code:</div>\n"
                    . "<div class=\"bbcode_code_body\" style=\"white-space:pre\">A [b]and[/b] &amp; &lt;woo&gt;!\n"
                    . "\tAnd a ['hey'] and a [/nonny] and a ho ho ho!</div>\n"
                    . "</div>\n"
                    . "Also not code.",
            ]],
            [[
                'descr' => "[code]...[/code] should reproduce PHP source code undamaged.",
                'bbcode' => "Not code.\n"
                    . "[code]\n"
                    . "\$foo['bar'] = 42;\n"
                    . "if (\$foo[\"bar\"] < 42) \$foo[] = 0;\n"
                    . "[/code]\n"
                    . "Also not code.\n",
                'html' => "Not code."
                    . "\n<div class=\"bbcode_code\">\n"
                    . "<div class=\"bbcode_code_head\">Code:</div>\n"
                    . "<div class=\"bbcode_code_body\" style=\"white-space:pre\">\$foo['bar'] = 42;\n"
                    . "if (\$foo[&quot;bar&quot;] &lt; 42) \$foo[] = 0;</div>\n"
                    . "</div>\n"
                    . "Also not code.<br>\n",
            ]],
            [[
                'descr' => "<code>...</code> should not misbehave in '<' tag marker mode.",
                'bbcode' => "Not code."
                    . "<code>A <b>and</b> & <woo>!\n\tAnd a [hey] and a [/nonny] and a ho ho ho!</code>"
                    . "Also not code.",
                'html' => "Not code."
                    . "\n<div class=\"bbcode_code\">\n"
                    . "<div class=\"bbcode_code_head\">Code:</div>\n"
                    . "<div class=\"bbcode_code_body\" style=\"white-space:pre\">A &lt;b&gt;and&lt;/b&gt; &amp; &lt;woo&gt;!\n"
                    . "\tAnd a [hey] and a [/nonny] and a ho ho ho!</div>\n"
                    . "</div>\n"
                    . "Also not code.",
                'tag_marker' => '<',
            ]],
            [[
                'descr' => "[quote]...[/quote] should produce a plain quote.",
                'bbcode' => "Outside the quote."
                    . "[quote]A [b]and[/b] & <woo>!\n\tAnd a [hey] and a [/nonny] and a ho ho ho![/quote]"
                    . "Also outside the quote.",
                'html' => "Outside the quote."
                    . "\n<div class=\"bbcode_quote\">\n"
                    . "<div class=\"bbcode_quote_head\">Quote:</div>\n"
                    . "<div class=\"bbcode_quote_body\">A <b>and</b> &amp; &lt;woo&gt;!<br>\n"
                    . "And a [hey] and a [/nonny] and a ho ho ho!</div>\n"
                    . "</div>\n"
                    . "Also outside the quote.",
            ]],
            [[
                'descr' => "Multiple nested [quote]...[/quote] tags should produce nested quotes.",
                'bbcode' => "text0\n[quote]\n[quote]\n[quote]text1[/quote]\ntext2[/quote]\ntext3[/quote]\ntext4",
                'html' => "text0"
                    . "\n<div class=\"bbcode_quote\">\n"
                    . "<div class=\"bbcode_quote_head\">Quote:</div>\n"
                    . "<div class=\"bbcode_quote_body\">"
                    . "\n<div class=\"bbcode_quote\">\n"
                    . "<div class=\"bbcode_quote_head\">Quote:</div>\n"
                    . "<div class=\"bbcode_quote_body\">"
                    . "\n<div class=\"bbcode_quote\">\n"
                    . "<div class=\"bbcode_quote_head\">Quote:</div>\n"
                    . "<div class=\"bbcode_quote_body\">"
                    . "text1"
                    . "</div>\n"
                    . "</div>\n"
                    . "text2"
                    . "</div>\n"
                    . "</div>\n"
                    . "text3"
                    . "</div>\n"
                    . "</div>\n"
                    . "text4",
            ]],
            [[
                'descr' => "Multiple nested [quote]...[/quote] tags should produce nested quotes.",
                'bbcode' => "[quote]\n[quote]\n[quote]text1[/quote]\ntext2[/quote]\ntext3[/quote]\ntext4 :) text5 :o text6 :o",
                'html' => "\n<div class=\"bbcode_quote\">\n"
                    . "<div class=\"bbcode_quote_head\">Quote:</div>\n"
                    . "<div class=\"bbcode_quote_body\">"
                    . "\n<div class=\"bbcode_quote\">\n"
                    . "<div class=\"bbcode_quote_head\">Quote:</div>\n"
                    . "<div class=\"bbcode_quote_body\">"
                    . "\n<div class=\"bbcode_quote\">\n"
                    . "<div class=\"bbcode_quote_head\">Quote:</div>\n"
                    . "<div class=\"bbcode_quote_body\">"
                    . "text1"
                    . "</div>\n"
                    . "</div>\n"
                    . "text2"
                    . "</div>\n"
                    . "</div>\n"
                    . "text3"
                    . "</div>\n"
                    . "</div>\n"
                    . "text4 <img src=\"smileys/smile.gif\" alt=\":)\" title=\":)\" class=\"bbcode_smiley\" /> text5 :o text6 :o",
            ]],
            [[
                'descr' => "[quote=John]...[/quote] should produce a quote from John.",
                'bbcode' => "Outside the quote."
                    . "[quote=John]A [b]and[/b] & <woo>!\n\tAnd a [hey] and a [/nonny] and a ho ho ho![/quote]"
                    . "Also outside the quote.",
                'html' => "Outside the quote."
                    . "\n<div class=\"bbcode_quote\">\n"
                    . "<div class=\"bbcode_quote_head\">John wrote:</div>\n"
                    . "<div class=\"bbcode_quote_body\">A <b>and</b> &amp; &lt;woo&gt;!<br>\n"
                    . "And a [hey] and a [/nonny] and a ho ho ho!</div>\n"
                    . "</div>\n"
                    . "Also outside the quote.",
            ]],
            [[
                'descr' => "[quote=\"John Smith\"]...[/quote] should produce a quote from John Smith.",
                'bbcode' => "Outside the quote."
                    . "[quote=\"John Smith\"]A [b]and[/b] & <woo>!\n\tAnd a [hey] and a [/nonny] and a ho ho ho![/quote]"
                    . "Also outside the quote.",
                'html' => "Outside the quote."
                    . "\n<div class=\"bbcode_quote\">\n"
                    . "<div class=\"bbcode_quote_head\">John Smith wrote:</div>\n"
                    . "<div class=\"bbcode_quote_body\">A <b>and</b> &amp; &lt;woo&gt;!<br>\n"
                    . "And a [hey] and a [/nonny] and a ho ho ho!</div>\n"
                    . "</div>\n"
                    . "Also outside the quote.",
            ]],
            [[
                'descr' => "[quote name= date= url=]...[/quote] should produce a detailed quote.",
                'bbcode' => "Outside the quote."
                    . "[quote name=\"John Smith\" date=\"July 4, 1776\" url=\"http://www.constitution.gov\"]We hold these truths to be self-evident...[/quote]"
                    . "Also outside the quote.",
                'html' => "Outside the quote."
                    . "\n<div class=\"bbcode_quote\">\n"
                    . "<div class=\"bbcode_quote_head\"><a href=\"http://www.constitution.gov\">John Smith wrote on July 4, 1776:</a></div>\n"
                    . "<div class=\"bbcode_quote_body\">We hold these truths to be self-evident...</div>\n"
                    . "</div>\n"
                    . "Also outside the quote.",
            ]],
            [[
                'descr' => "[quote name= date= url=]...[/quote] should disallow bad URLs.",
                'bbcode' => "Outside the quote."
                    . "[quote name=\"John Smith\" date=\"July 4, 1776\" url=\"javascript:alert()\"]We hold these truths to be self-evident...[/quote]"
                    . "Also outside the quote.",
                'html' => "Outside the quote."
                    . "\n<div class=\"bbcode_quote\">\n"
                    . "<div class=\"bbcode_quote_head\">John Smith wrote on July 4, 1776:</div>\n"
                    . "<div class=\"bbcode_quote_body\">We hold these truths to be self-evident...</div>\n"
                    . "</div>\n"
                    . "Also outside the quote.",
            ]],
            [[
                'descr' => "[quote=\"<script>javascript:alert()</script>\"] should not produce Javascript.",
                'bbcode' => "Outside the quote."
                    . "[quote=\"<script>javascript:alert()</script>\"]A [b]and[/b] & <woo>!\n\tAnd a [hey] and a [/nonny] and a ho ho ho![/quote]"
                    . "Also outside the quote.",
                'html' => "Outside the quote."
                    . "\n<div class=\"bbcode_quote\">\n"
                    . "<div class=\"bbcode_quote_head\">&lt;script&gt;javascript:alert()&lt;/script&gt; wrote:</div>\n"
                    . "<div class=\"bbcode_quote_body\">A <b>and</b> &amp; &lt;woo&gt;!<br>\n"
                    . "And a [hey] and a [/nonny] and a ho ho ho!</div>\n"
                    . "</div>\n"
                    . "Also outside the quote.",
            ]],
            [[
                'descr' => "[columns] should produce columns.",
                'bbcode' => "Before the columns."
                    . "[columns]This is a test.[nextcol]This is [b]beside[/b] it.[nextcol]This is [i]also[/i] beside it.[/columns]"
                    . "After the columns.",
                'html' => "Before the columns."
                    . "\n<table class=\"bbcode_columns\"><tbody><tr><td class=\"bbcode_column bbcode_firstcolumn\">\n"
                    . "This is a test."
                    . "\n</td><td class=\"bbcode_column\">\n"
                    . "This is <b>beside</b> it."
                    . "\n</td><td class=\"bbcode_column\">\n"
                    . "This is <i>also</i> beside it."
                    . "\n</td></tr></tbody></table>\n"
                    . "After the columns.",
            ]],
            [[
                'descr' => "[nextcol] doesn't do anything outside a [columns] block.",
                'bbcode' => "Here's some text.[nextcol]\nHere's some more.\n",
                'html' => "Here's some text.[nextcol]<br>\nHere's some more.<br>\n",
            ]],
            [[
                'descr' => "Bad column misuse doesn't break layouts.",
                'bbcode' => "[center][columns]This is a test.[nextcol]This is also a [b]test[/b].[/center][/columns]",
                'html' => "\n<div class=\"bbcode_center\" style=\"text-align:center\">\n"
                    . "\n<table class=\"bbcode_columns\"><tbody><tr><td class=\"bbcode_column bbcode_firstcolumn\">\n"
                    . "This is a test."
                    . "\n</td><td class=\"bbcode_column\">\n"
                    . "This is also a <b>test</b>."
                    . "\n</td></tr></tbody></table>\n"
                    . "\n</div>\n",
            ]],
        ];
        return $result;
    }

    /**
     * Provide list and list-item test data.
     *
     * @return array Returns the test function arguments.
     */
    public function provideListAndListItemTests() {
        $result = [
            [[
                'descr' => "[list] and [*] should produce an unordered list.",
                'bbcode' => "[list][*]One Box[*]Two Boxes[*]Three Boxes[/list]",
                'html' => "\n<ul class=\"bbcode_list\">\n<li>One Box</li>\n<li>Two Boxes</li>\n<li>Three Boxes</li>\n</ul>\n",
            ]],
            [[
                'descr' => "[list] and [*] should produce an unordered list even without [/list].",
                'bbcode' => "[list][*]One Box[*]Two Boxes[*]Three Boxes",
                'html' => "\n<ul class=\"bbcode_list\">\n<li>One Box</li>\n<li>Two Boxes</li>\n<li>Three Boxes</li>\n</ul>\n",
            ]],
            [[
                'descr' => "[list=circle] should produce an unordered list.",
                'bbcode' => "[list=circle][*]One Box[*]Two Boxes[*]Three Boxes[/list]",
                'html' => "\n<ul class=\"bbcode_list\" style=\"list-style-type:circle\">\n<li>One Box</li>\n<li>Two Boxes</li>\n<li>Three Boxes</li>\n</ul>\n",
            ]],
            [[
                'descr' => "[list=disc] should produce an unordered list.",
                'bbcode' => "[list=disc][*]One Box[*]Two Boxes[*]Three Boxes[/list]",
                'html' => "\n<ul class=\"bbcode_list\" style=\"list-style-type:disc\">\n<li>One Box</li>\n<li>Two Boxes</li>\n<li>Three Boxes</li>\n</ul>\n",
            ]],
            [[
                'descr' => "[list=square] should produce an unordered list.",
                'bbcode' => "[list=square][*]One Box[*]Two Boxes[*]Three Boxes[/list]",
                'html' => "\n<ul class=\"bbcode_list\" style=\"list-style-type:square\">\n<li>One Box</li>\n<li>Two Boxes</li>\n<li>Three Boxes</li>\n</ul>\n",
            ]],
            [[
                'descr' => "[list=1] should produce an ordered list.",
                'bbcode' => "[list=1][*]One Box[*]Two Boxes[*]Three Boxes[/list]",
                'html' => "\n<ol class=\"bbcode_list\">\n<li>One Box</li>\n<li>Two Boxes</li>\n<li>Three Boxes</li>\n</ol>\n",
            ]],
            [[
                'descr' => "[list=A] should produce an ordered list.",
                'bbcode' => "[list=A][*]One Box[*]Two Boxes[*]Three Boxes[/list]",
                'html' => "\n<ol class=\"bbcode_list\" style=\"list-style-type:upper-alpha\">\n<li>One Box</li>\n<li>Two Boxes</li>\n<li>Three Boxes</li>\n</ol>\n",
            ]],
            [[
                'descr' => "[list=a] should produce an ordered list.",
                'bbcode' => "[list=a][*]One Box[*]Two Boxes[*]Three Boxes[/list]",
                'html' => "\n<ol class=\"bbcode_list\" style=\"list-style-type:lower-alpha\">\n<li>One Box</li>\n<li>Two Boxes</li>\n<li>Three Boxes</li>\n</ol>\n",
            ]],
            [[
                'descr' => "[list=I] should produce an ordered list.",
                'bbcode' => "[list=I][*]One Box[*]Two Boxes[*]Three Boxes[/list]",
                'html' => "\n<ol class=\"bbcode_list\" style=\"list-style-type:upper-roman\">\n<li>One Box</li>\n<li>Two Boxes</li>\n<li>Three Boxes</li>\n</ol>\n",
            ]],
            [[
                'descr' => "[list=i] should produce an ordered list.",
                'bbcode' => "[list=i][*]One Box[*]Two Boxes[*]Three Boxes[/list]",
                'html' => "\n<ol class=\"bbcode_list\" style=\"list-style-type:lower-roman\">\n<li>One Box</li>\n<li>Two Boxes</li>\n<li>Three Boxes</li>\n</ol>\n",
            ]],
            [[
                'descr' => "[list=greek] should produce an ordered list.",
                'bbcode' => "[list=greek][*]One Box[*]Two Boxes[*]Three Boxes[/list]",
                'html' => "\n<ol class=\"bbcode_list\" style=\"list-style-type:lower-greek\">\n<li>One Box</li>\n<li>Two Boxes</li>\n<li>Three Boxes</li>\n</ol>\n",
            ]],
            [[
                'descr' => "[list=georgian] should produce an ordered list.",
                'bbcode' => "[list=georgian][*]One Box[*]Two Boxes[*]Three Boxes[/list]",
                'html' => "\n<ol class=\"bbcode_list\" style=\"list-style-type:georgian\">\n<li>One Box</li>\n<li>Two Boxes</li>\n<li>Three Boxes</li>\n</ol>\n",
            ]],
            [[
                'descr' => "[list=armenian] should produce an ordered list.",
                'bbcode' => "[list=armenian][*]One Box[*]Two Boxes[*]Three Boxes[/list]",
                'html' => "\n<ol class=\"bbcode_list\" style=\"list-style-type:armenian\">\n<li>One Box</li>\n<li>Two Boxes</li>\n<li>Three Boxes</li>\n</ol>\n",
            ]],
        ];
        return $result;
    }

    /**
     * Run a given test array.
     *
     * @param array $test The test to run.
     */
    protected function performTest($test) {
        $testDefaults = [
            'newline_ignore' => false,
            'detect_urls' => false,
            'urltarget' => false,
            'urlforcetarget' => false,
            'plainmode' => false,
            'tag_marker' => '',
            'skip' => false

        ];
        $test = array_replace($testDefaults, $test);

        if ($test['skip']) {
            $this->markTestSkipped('Skipped test: '.$test['descr']);
            return;
        }

        $bbcode = new BBCode();

        $bbcode->addRule('wstest', [
            'mode' => BBCode::BBCODE_MODE_ENHANCED,
            'allow' => ['_default' => '/^[a-zA-Z0-9._ -]+$/'],
            'template' => '<span style="wstest:{$_default}">{$_content}</span>',
            'class' => 'inline',
            'allow_in' => ['listitem', 'block', 'columns', 'inline', 'link'],
        ]);

        $bbcode->setLocalImgDir(__DIR__.'/../smileys');
        $bbcode->setLocalImgURL('smileys');
        $bbcode->setTagMarker('[');
        $bbcode->setAllowAmpersand(false);
        $bbcode->setIgnoreNewlines((bool)$test['newline_ignore']);
        $bbcode->setDetectURLs((bool)$test['detect_urls']);
        $bbcode->setURLTargetable($test['urltarget'] == true);
        $bbcode->setURLTarget($test['urlforcetarget']);
        $bbcode->setPlainMode($test['plainmode']);

        if ($test['tag_marker'] === '<') {
            $bbcode->setTagMarker('<');
            $bbcode->setAllowAmpersand(true);
        } elseif (!empty($test['tag_marker'])) {
            $bbcode->setTagMarker($test['tag_marker']);
        }

        $result = $bbcode->parse($test['bbcode']);
        if (!empty($test['regex'])) {
            $this->assertRegExp($test['regex'], $result, $test['descr']);
        } else {
            $this->assertSame($test['html'], $result, $test['descr']);
        }
    }

    /**
     * Test input validation.
     *
     * @param array $test The test to perform.
     * @dataProvider provideInputValidationTests
     */
    public function testInputValidation(array $test) {
        $this->performTest($test);
    }

    /**
     * Test special characters.
     *
     * @param array $test The test to perform.
     * @dataProvider provideSpecialCharacterTests
     */
    public function testSpecialCharacters(array $test) {
        $this->performTest($test);
    }

    /**
     * Test whitespace edge cases.
     *
     * @param array $test The test to perform.
     * @dataProvider provideWhitespaceTests
     */
    public function testWhitespace(array $test) {
        $this->performTest($test);
    }

    /**
     * Test inline tag conversions.
     *
     * @param array $test The test to perform.
     * @dataProvider provideInlineConversionTests
     */
    public function testInlineConversions(array $test) {
        $this->performTest($test);
    }

    /**
     * Test **[url]** tags.
     *
     * @param array $test The test to perform.
     * @dataProvider provideUrlTests
     */
    public function testUrls(array $test) {
        $this->performTest($test);
    }

    /**
     * Test auto-generated URLs.
     *
     * @param array $test The test to perform.
     * @dataProvider provideEmbeddedUrlTests
     */
    public function testEmbeddedUrls(array $test) {
        $this->performTest($test);
    }

    /**
     * Test tags that generate links.
     *
     * @param array $test The test to perform.
     * @dataProvider provideUrlLikeTagTests
     */
    public function testUrlLikeTags(array $test) {
        $this->performTest($test);
    }

    /**
     * Test **[img]** tags.
     *
     * @param array $test The test to perform.
     * @dataProvider provideImageTests
     */
    public function testImages(array $test) {
        $this->performTest($test);
    }

    /**
     * Test block tag conversions.
     *
     * @param array $test The test to perform.
     * @dataProvider provideBlockTagConversionTests
     */
    public function testBlockTagConversions(array $test) {
        $this->performTest($test);
    }

    /**
     * Test lists and list items.
     *
     * @param array $test The test to perform.
     * @dataProvider provideListAndListItemTests
     */
    public function testListAndListItems(array $test) {
        $this->performTest($test);
    }
}
