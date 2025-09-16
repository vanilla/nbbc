
<a name="intro"></a>
<h1><img src='nbbc-small.png' width='206' height='56' alt='NBBC' /><br />The New BBCode Parser</h1>
<p class='copyright'>Version {$BBCODE_VERSION}<br />
Copyright &copy; 2008-10, the Phantom Inker.  All rights reserved.</p>

<p>The New BBCode Parser (NBBC) is a fully-validating, high-speed, extensible parser
for the BBCode document language, written in PHP, compatible with PHP 7.1+.
It converts BBCode input into HTML output, and can guarantee that the output will be
fully conformant to the XHTML 1.0 Strict standard no matter how badly-mangled the BBCode
input is.</p>

<p>What is BBCode?  BBCode is a document-formatting language, similar to HTML,
only designed to be much simpler to use.  BBCode was popularized by various forum and
bulletin-board services throughout the late 1990s and early 2000s, and is now a
de-facto standard; however, most BBCode parsers, which convert that BBCode into
displayable HTML, are non-validating, meaning that they do not guarantee that their
output will necessarily be &quot;good&quot; HTML.  Some are so over-simplified that
they are vulnerable to a number of security attacks.  NBBC is designed to be an easy
drop-in replacement for most existing BBCode parsers, and is designed to be both
validating and secure.</p>

<p>NBBC comes built-in with a number of useful features:</p>

<ul>
<li>Output is always XHTML 1.0 Strict conformant.</li>
<li>Output is protected against many common user-input attacks, such as XSS attacks.</li>
<li>Smileys, such as <tt>:-)</tt>, are converted into <tt>&lt;img&gt;</tt> tags,
	and a large library of common smiley images is included.</li>
<li>Includes a library supporting all the standard BBCode codes found on most
	popular bulletin boards and web forums.</li>
<li>Supports "wiki links," which can be an easy way for users to reference wiki
	pages on your site if you also have a wiki installed.</li>
<li>The list of codes is fully extensible; you can add and remove codes at any time.</li>
<li>Tightly encapsulated in classes and doesn't pollute the global namespace,
	so it's easy to drop into existing environments.</li>
<li>The entire parser consists of only four files, only one of which you ever need
	to include directly, which also makes it easy to drop into existing environments.</li>
<li>The standard library includes support for not only extremely common tags like [b]
	and [i], but also supports nearly all the tags found on most major forums and
	bulletin boards:  [center], [list], [code], and [quote], among others, with all
	their various flags and parameters.</li>
<li>Best of all, it's free!  NBBC is covered under the
	<a href="http://www.opensource.org/licenses/bsd-license.php">New BSD Open-Source License</a>,
	so it can be used anywhere, anywhen, in any project, for any reason.</li>
</ul>
