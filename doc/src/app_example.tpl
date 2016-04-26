
<h4>Common Rule Types</h4>

<div class='code_header'>A simple BBCode --&gt; HTML rule:</div>
<xmp class='code'>$bbcode->AddRule('mono', array(
    'simple_start' => '<tt>',
    'simple_end' => '</tt>',
    'class' => 'inline',
    'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
));</xmp>

<div class='code_header'>An enhanced rule, with parameter validation and default values:</div>
<xmp class='code'>$bbcode->AddRule('border',  Array(
    'mode' => BBCODE_MODE_ENHANCED,
    'template' => '<div style="border: {$size}px solid {$color}">{$_content}</div>',
    'allow' => Array(
        'color' => '/^#[0-9a-fA-F]+|[a-zA-Z]+$/',
        'size' => '/^[1-9][0-9]*$/',
    ),
    'default' => Array(
        'color' => 'blue',
        'size' => '1',
    ),
    'class' => 'block',
    'allow_in' => Array('listitem', 'block', 'columns'),
));</xmp>

<div class='code_header'>A callback rule:</div>
<xmp class='code'>$bbcode->AddRule('border',  Array(
    'mode' => BBCODE_MODE_CALLBACK,
    'method' => 'MyBorderFunction',
    'class' => 'block',
    'allow_in' => Array('listitem', 'block', 'columns'),
));

function MyBorderFunction($bbcode, $action, $name,
    $default, $params, $content) {
    ....
}</xmp>

<div class='code_header'>A callback rule to a method of an object:</div>
<xmp class='code'>class MyObject {
    public function BlargFunction($bbcode, $action, $name,
        $default, $params, $content) {
        ....
    }
}

$object = new MyObject;

$bbcode->AddRule('blarg',  Array(
    'mode' => BBCODE_MODE_CALLBACK,
    'method' => array($object, 'BlargFunction'),
    'class' => 'block',
    'allow_in' => Array('listitem', 'block', 'columns'),
));</xmp>

<hr />

<h4>Common Class Types</h4>

<p>If your tag is a new <b>replaced item</b> tag (like [img], for example), you'll probably want to use these classes:</p>
<xmp class='code'>array(
    ...
    'class' => 'image',
    'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
    ...
)</xmp>

<p>If your tag is a new <b>inline style</b> tag (like [b] or [font], for example), you'll probably want to use these classes:</p>
<xmp class='code'>array(
    ...
    'class' => 'inline',
    'allow_in' => Array('listitem', 'block', 'columns', 'inline', 'link'),
    ...
)</xmp>

<p>If your tag is a new <b>link</b> tag (like [url] or [email], for example), you'll probably want to use these classes:</p>
<xmp class='code'>array(
    ...
    'class' => 'link',
    'allow_in' => Array('listitem', 'block', 'columns', 'inline'),
    ...
)</xmp>

<p>If your tag is a new <b>block</b> tag (like [quote] or [center], for example), you'll probably want to use these classes:</p>
<xmp class='code'>array(
    ...
    'class' => 'block',
    'allow_in' => Array('listitem', 'block', 'columns'),
    ...
)</xmp>

<p>Regardless of which classes you use, it is still important to check to make sure that your new
tag can only go inside other tags where it's legal:  For example, if your tag outputs a <tt>&lt;span&gt;</tt>,
it's legal inside another tag that outputs a <tt>&lt;div&gt;</tt>, but not the other way around.  The rules
above are good rules of thumb, but it's still important to check.  (For example, you should not allow the
user to place a Flash animation inside an <tt>&lt;a&nbsp;href="..."&gt;...&lt;/a&gt;</tt> element, which
means that if you add a <tt>[flash]</tt> tag, it's of class "<tt>image</tt>" but is <i>not</i> allowed
inside class "<tt>link</tt>" like <tt>[img]</tt> is.  Always carefully check to see how your new tags
affect the class hierarchy and the resulting HTML.)</p>
