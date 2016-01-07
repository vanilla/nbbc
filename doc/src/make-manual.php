<?php

	require_once __DIR__.'/../../vendor/autoload.php';

	//-------------------------------------------------------------------------------------------
	//  Read "toc.tpl" and convert it into a hierarchy.

	$Groups = Array();

	$tocfile = file("toc.tpl");
	if (!$tocfile)
		die("Unable to open 'toc.tpl' for reading.\n");

	$group = -1;
	$section = -1;
	$subsection = -1;
	foreach ($tocfile as $line) {
		$line = trim($line);
		if (preg_match("/^[ \t]*(\\*+)[ \t]*((?:[^:]+:)?)(.*)$/", $line, $matches)) {
			$stars = strlen($matches[1]);
			$name = $matches[2];
			$title = $matches[3];
			if (preg_match("/^([^#:]+)((?:#[^:]*)?):/", $name, $matches)) {
				$name = $matches[1];
				$link = $matches[2];
			}
			else $name = $link = "";
			switch ($stars) {
			case 1:
				++$group;
				$Groups[$group]
					= Array('name' => $name, 'link' => $link, 'title' => $title, 'sub' => Array());
				$section = -1;
				break;
			case 2:
				++$section;
				$Groups[$group]['sub'][$section]
					= Array('name' => $name, 'link' => $link, 'title' => $title, 'sub' => Array());
				$subsection = -1;
				break;
			case 3:
				++$subsection;
				$Groups[$group]['sub'][$section]['sub'][$subsection]
					= Array('name' => $name, 'link' => $link, 'title' => $title, 'sub' => Array());
				break;
			}
		}
	}

	//-------------------------------------------------------------------------------------------
	//  Generate the table of contents, recursively.

	function GenerateTocTree($branch, $depth, &$count, $class = "", $limit = 99999, $funkylink = false) {
		$list_types_by_depth = Array(
			'upper-roman',
			'upper-alpha',
			'decimal',
			'lower-alpha',
			'lower-roman',
		);

		$output = str_repeat("\t", $depth)
			. "<ol style=\"list-style-type:{$list_types_by_depth[$depth]}\"$class>\n";

		if ($depth == 0)
			$style = " style=\"margin-top:1em;\"";
		else $style = "";

		foreach ($branch as $node) {
			$title = trim($node['title']);
			if (isset($node['name']))
				$name = trim($node['name']);
			else $name = "";
			if (substr($title, 0, 1) == '*')
				$title = "<b>" . htmlspecialchars(trim(substr($title, 1))) . "</b>";
			else $title = htmlspecialchars($title);

			$output .= str_repeat("\t", $depth);

			if (strlen($name)) {
				if ($funkylink) {
					$output .= "<li$style><a href=\"readme.php?page=" . htmlspecialchars($name)
						. "\">$title</a>";
				}
				else {
					$output .= "<li$style><a href=\"" . htmlspecialchars($name) . ".html"
						. htmlspecialchars(trim(@$node['link'])) . "\" target=\"content\">$title</a>";
				}
			}
			else $output .= "<li$style>$title";
			$count++;

			if (count($node['sub']) && $depth+1 < $limit) {
				$output .= "\n";
				$output .= GenerateTocTree($node['sub'], $depth + 1, $count, "", $limit);
				$output .= str_repeat("\t", $depth);
			}

			$output .= "</li>\n";
		}

		$output .= str_repeat("\t", $depth) . "</ol>\n";

		return $output;
	}

	function GenerateShortTocTree($branch) {
		$output = "<table class='shorttoc'><tbody><tr><td>\n\n";

		$count = 0;
		foreach ($branch as $index => $node) {
			if ($count > 12) {
				$output .= "</td><td>\n\n";
				$count = 0;
			}

			$title = trim($node['title']);
			if (isset($node['name']))
				$name = trim($node['name']);
			else $name = "";
			if (substr($title, 0, 1) == '*')
				$title = "<b>" . RomanNumerals($index+1) . ". "
					. htmlspecialchars(trim(substr($title, 1))) . "</b>";
			else $title = RomanNumerals($index+1) . ". " . htmlspecialchars($title);

			$output .= "<div style='margin-bottom:1em;'>$title";
			$count++;

			if (count($node['sub'])) {
				$output .= "\n";
				$output .= GenerateTocTree($node['sub'], 1, $count, "", 2, true);
			}

			$output .= "</div>\n\n";
		}

		$output .= "</td></tr></tbody></table>\n";

		return $output;
	}

	$output = <<< EOI
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<title>Table of Contents - NBBC: The New BBCode Parser</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel='stylesheet' type='text/css' href='styles.css' />
</head>

<body>

<div class='toc'>


EOI;

	$count = 0;
	$output .= GenerateTocTree($Groups, 0, $count, " class=\"toc_outermost\"");

	$output .= <<< EOI

</div>

</body>
</html>

EOI;

	$tocfile = fopen("../toc.html", "w");
	fwrite($tocfile, $output);
	fclose($tocfile);

	$output = GenerateShortTocTree($Groups);
	$tocfile = fopen("../shorttoc.html", "w");
	fwrite($tocfile, $output);
	fclose($tocfile);

	//-------------------------------------------------------------------------------------------
	//  Generate each templated file, recursively.

	function FormatPage($group_index, $group_title, $section_index, $section_title,
		$content, $prev_url, $prev_title, $next_url, $next_title) {

		$group_index = htmlspecialchars($group_index);
		$section_index = htmlspecialchars($section_index);
		$group_title = htmlspecialchars($group_title);
		$section_title = htmlspecialchars($section_title);
		$prev_title = htmlspecialchars($prev_title);
		$prev_url = htmlspecialchars($prev_url);
		$next_title = htmlspecialchars($next_title);
		$next_url = htmlspecialchars($next_url);

		$year = 2010; // date("Y");

		$content = str_replace('{$BBCODE_VERSION}', BBCode::BBCODE_VERSION, $content);

		if ($prev_url)
			$prev = "Previous: <a href=\"$prev_url\">$prev_title</a>";
		else $prev = "";
		if ($next_url)
			$next = "Next: <a href=\"$next_url\">$next_title</a>";
		else $next = "";
		if ($prev_url && $next_url)
			$separator = " | ";
		else $separator = "";

		$output = <<< EOI
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<title>{$section_title} - NBBC: The New BBCode Parser</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel='stylesheet' type='text/css' href='styles.css' />
</head>

<body>

<div align='center'><div class='page_container2'><div class='page_container'>

<h2>{$group_index} {$group_title}</h2>

<p style='text-align:right;margin:0;'>[ $prev$separator$next ]</p>

<h3>{$section_index}. {$section_title}</h3>

{$content}

<p style='text-align:right;'>[ $prev$separator$next ]</p>

<hr />

<div style='text-align:center;'>Copyright &copy; {$year}, the Phantom Inker.  All rights reserved.</div>

</div></div></div>

</body>

</html>

EOI;

		return $output;
	}

	function RomanNumerals2($num) {
		if ($num == 6) return "Appendix ";
		return RomanNumerals($num) . ".";
	}

	function RomanNumerals($num) {
		$n = intval($num);
		$result = '';

		$lookup = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
			'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
			'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);

		foreach ($lookup as $roman => $value) {
			$matches = intval($n / $value);
			$result .= str_repeat($roman, $matches);
			$n = $n % $value;
		}

		return $result;
	}

	function GetPrevSection($group_index, $section_index, &$prev_group, &$prev_section) {
		global $Groups;
		if (--$section_index < 0) {
			if (--$group_index < 0)
				return $prev_group = $prev_section = false;
			$section_index = count($Groups[$group_index]['sub']) - 1;
		}
		$prev_group = $group_index;
		$prev_section = $section_index;
		return true;
	}

	function GetNextSection($group_index, $section_index, &$next_group, &$next_section) {
		global $Groups;
		if (++$section_index >= count($Groups[$group_index]['sub'])) {
			if (++$group_index >= count($Groups))
				return $next_group = $next_section = false;
			$section_index = 0;
		}
		$next_group = $group_index;
		$next_section = $section_index;
		return true;
	}

	print "Creating the user manual...\n";

	foreach ($Groups as $group_index => $group) {
		$group_title = trim($group['title']);
		if (substr($group_title, 0, 1) == '*')
			$group_title = trim(substr($group_title, 1));
		foreach ($group['sub'] as $section_index => $section) {
			print "{$section['name']}.tpl\n";
			$content = trim(file_get_contents("{$section['name']}.tpl"));
			$group_id = RomanNumerals($group_index+1) . ".";
			$section_id = chr($section_index + 65);
			$section_title = trim($section['title']);
			if (substr($section_title, 0, 1) == '*')
				$section_title = trim(substr($section_title, 1));

			if (GetPrevSection($group_index, $section_index, $prev_group, $prev_section)) {
				$prev_group_id = RomanNumerals2($prev_group + 1);
				$prev_section_id = chr($prev_section + 65);
				$prev_url = $Groups[$prev_group]['sub'][$prev_section]['name'] . ".html";
				$prev_title = trim($Groups[$prev_group]['sub'][$prev_section]['title']);
				if (substr($prev_title, 0, 1) == '*')
					$prev_title = trim(substr($prev_title, 1));
				if ($prev_group == $group_index)
					$prev_title = "{$prev_section_id}. $prev_title";
				else $prev_title = "{$prev_group_id}{$prev_section_id}. $prev_title";
			}
			else $prev_url = $prev_title = "";
			if (GetNextSection($group_index, $section_index, $next_group, $next_section)) {
				$next_group_id = RomanNumerals2($next_group + 1);
				$next_section_id = chr($next_section + 65);
				$next_url = $Groups[$next_group]['sub'][$next_section]['name'] . ".html";
				$next_title = trim($Groups[$next_group]['sub'][$next_section]['title']);
				if (substr($next_title, 0, 1) == '*')
					$next_title = trim(substr($next_title, 1));
				if ($next_group == $group_index)
					$next_title = "{$next_section_id}. $next_title";
				else $next_title = "{$next_group_id}{$next_section_id}. $next_title";
			}
			else $next_url = $next_title = "";

			$output = FormatPage($group_id, $group_title, $section_id, $section_title, $content,
				$prev_url, $prev_title, $next_url, $next_title);
			$file = fopen("../{$section['name']}.html", "w");
			if (!$file) {
				print "Warning: Unable to open \"../{$section['name']}.html\" for writing.\n";
				continue;
			}
			fwrite($file, $output);
			fclose($file);
		}
	}

?>
