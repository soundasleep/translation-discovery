<?php

/**
 * Search through sets of possible components for files that
 * use translation strings, either through t("..."), ht("..."), plural("...", "...")
 * or with an "i18n" block comment directly after a string, and write this out to
 * a template file.
 */

require(__DIR__ . "/functions.php");

if (count($argv) < 2) {
  throw new Exception("Needs file root parameter");
}

$root = $argv[1];
if (!file_exists($root . "/translation-discovery.json")) {
  throw new Exception("No translation-discovery.json found in '$root'");
}

$json = json_decode(file_get_contents($root . "/translation-discovery.json"), true /* assoc */);

// add default parameters
$json += array(
  'templates' => 'vendor/*/*',
  'template' => 'locale/template.json',
  'templates_ignore' => array('/test/', '/tests/'),
  'translation_functions' => array(),     // additional translation functions
  'depth' => 3
);

if (!is_array($json['templates'])) {
  $json['templates'] = array($json['templates']);
}

// make target directories as necessary
make_target_directories(array($json['template']));

// now load all of the components
$all_dirs = array();
foreach ($json['templates'] as $dir) {
  $all_dirs = array_merge($all_dirs, get_all_directories($root . "/" . $dir, $json['depth']));
}
echo "Found " . count($all_dirs) . " potential subdirectories\n";

// this function is from Openclerk::I18n
function strip_i18n_key($key) {
  $key = preg_replace("/[\r\n]+/im", " ", $key);
  $key = preg_replace("/[\\s]{2,}/im", " ", $key);
  return trim($key);
}

function strip_comments($text) {
  $text = preg_replace("#([^:])//[^\n]+#ims", "\\1", $text);
  // only strip phpdoc comments
  $text = preg_replace("#/\\*\\*.+?\\*/#ims", "", $text);
  return $text;
}

// iterate over all subdirectories for .php files
$found = array();
foreach ($all_dirs as $dir) {
  // get all files
  $files = get_all_immediate_files($dir, ".php");

  foreach ($files as $f) {
    // don't look within ignored folders
    $should_ignore = false;
    foreach ($json['templates_ignore'] as $should_ignore_dir) {
      if (strpos(str_replace("\\", "/", $f), $should_ignore_dir) !== false) {
        $should_ignore = true;
      }
    }
    if ($should_ignore) {
      continue;
    }

    $input = file_get_contents($f);
    $input = strip_comments($input);

    $translation_functions = array_merge(
      $json['translation_functions'],
      array('t', 'ht')
    );

    // find instances of t() and ht()
    foreach ($translation_functions as $translation_function) {
      $matches = false;
      if (preg_match_all("#[ \t\n(]" . $translation_function . "\\((|['\"][^\"]+[\"'],[ \t\n])\"([^\"]+)\"(|,[ \t\n].+?)\\)#ims", $input, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
          // remove whitespace that will never display
          $match[2] = strip_i18n_key($match[2]);
          $found[$match[2]] = $match[2];
        }
      }
      if (preg_match_all("#[ \t\n(]" . $translation_function . "\\((|['\"][^\"]+[\"'],[ \t\n])'([^']+)'(|,[ \t\n].+?)\\)#ims", $input, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
          // remove whitespace that will never display
          $match[2] = strip_i18n_key($match[2]);
          $found[$match[2]] = $match[2];
        }
      }
    }

    // find instances of plural()
    if (preg_match_all("#[ \t\n(]plural\\(\"([^\"]+)\",[ \t\n][^\"].+?\\)#ims", $input, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        // remove whitespace that will never display
        $match[1] = strip_i18n_key($match[1]);
        $found[$match[1]] = $match[1];
        $found[$match[1] . "s"] = $match[1] . "s";
      }
    }
    if (preg_match_all("#[ \t\n(]plural\\('([^']+)',[ \t\n][^'].+?\\)#ims", $input, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        // remove whitespace that will never display
        $match[1] = strip_i18n_key($match[1]);
        $found[$match[1]] = $match[1];
        $found[$match[1] . "s"] = $match[1] . "s";
      }
    }
    if (preg_match_all("#[ \t\n(]plural\\(\"([^\"]+)\",[ \t\n]\"([^\"]+)\",[ \t\n][^\"].+?\\)#ims", $input, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        // remove whitespace that will never display
        $match[1] = strip_i18n_key($match[1]);
        $match[2] = strip_i18n_key($match[2]);
        $found[$match[1]] = $match[1];
        $found[$match[2]] = $match[2];
      }
    }
    if (preg_match_all("#[ \t\n(]plural\\('([^']+)',[ \t\n]'([^']+)',[ \t\n][^'].+?\\)#ims", $input, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        // remove whitespace that will never display
        $match[1] = strip_i18n_key($match[1]);
        $match[2] = strip_i18n_key($match[2]);
        $found[$match[1]] = $match[1];
        $found[$match[2]] = $match[2];
      }
    }

    // find instances of "string" /* i18n */
    if (preg_match_all("#\"([^\"]+)\" /\\* i18n \\*/#ims", $input, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        // remove whitespace that will never display
        $match[2] = strip_i18n_key($match[1]);
        $found[$match[1]] = $match[1];
      }
    }
    if (preg_match_all("#'([^']+)' /\\* i18n \\*/#ims", $input, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        // remove whitespace that will never display
        $match[2] = strip_i18n_key($match[1]);
        $found[$match[1]] = $match[1];
      }
    }
  }
}

// we can't have any keys that use HTML like <i>: conversion to/from google will mess them up into :i placeholders
foreach ($found as $value) {
  if (preg_match("#</?[a-z]+>#im", $value)) {
    throw new Exception("i18n key '" . $value . "' uses HTML, which is not allowed");
  }
}

// find all PHP files that could be included
echo "Found " . count($found) . " potential translation keys\n";

// sort
ksort($found);

// print out to a JSON file
$fp = fopen($root . "/" . $json['template'], "w");
if (!$fp) {
  throw new Exception("Could not open destination file '" . $json['template'] . "' for writing");
}

// we write this out manually so we can format it
write_out_formatted_json($fp, $found);
fclose($fp);

echo "Wrote template file '" . $json['template'] . "'\n";
