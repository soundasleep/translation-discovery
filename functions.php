<?php

function get_directories_to_search($dirs, $pattern) {
  // convert pattern into a regular expression
  $pattern = str_replace("*", "[^/]+", $pattern);
  $pattern = "#" . $pattern . "$#";

  // find all matching directories
  $result = array();
  foreach ($dirs as $dir) {
    if (preg_match($pattern, $dir)) {
      $result[] = $dir;
    }
  }

  return $result;
}

function get_all_directories($root, $max_depth = 3) {
  $result = array($root);
  if ($handle = opendir($root)) {
    while (false !== ($entry = readdir($handle))) {
      if ($entry != "." && $entry != ".." && is_dir($root . "/" . $entry)) {
          $result[] = $root . "/" . $entry;
          if ($max_depth > 1) {
            $result = array_merge($result, get_all_directories($root . "/" . $entry, $max_depth - 1));
          }
        }
    }
    closedir($handle);
  }
  return $result;
}

function get_all_immediate_files($root, $extension) {
  $result = array();
  if ($handle = opendir($root)) {
    while (false !== ($entry = readdir($handle))) {
      if ($entry != "." && $entry != ".." && substr(strtolower($entry), -strlen($extension)) == strtolower($extension) && !is_dir($root . "/" . $entry)) {
          $result[] = $root . "/" . $entry;
        }
    }
    closedir($handle);
  }
  return $result;
}

function make_target_directories($dirs) {
  foreach ($dirs as $path) {
    // remove any filenames
    if (substr($path, -1) !== "/") {
      $path = substr($path, 0, strrpos($path, "/"));
    }
    if (!file_exists($path)) {
      echo "Making directory '$path' recursively...\n";
      mkdir($path, 0777, true);
    }
  }
}

function get_matching_paths($dir, $path) {
  // a simple way to match paths: just find all files that match, and
  // then filter
  $result = get_all_files($dir);

  // filter out
  $filtered = array();
  foreach ($result as $value) {
    if (fnmatch($dir . "/" . $path, $value)) {
      $filtered[] = $value;
    }
  }

  // sort and renumber
  sort($filtered);

  return $filtered;
}

function get_all_files($root, $max_depth = 3) {
  $result = array();
  if ($handle = opendir($root)) {
    while (false !== ($entry = readdir($handle))) {
      if ($entry != "." && $entry != "..") {
          if (is_dir($root . "/" . $entry)) {
            if ($max_depth > 1) {
              $result = array_merge($result, get_all_files($root . "/" . $entry, $max_depth - 1));
            }
          } else {
            $result[] = $root . "/" . $entry;
          }
        }
    }
    closedir($handle);
  }
  return $result;
}

/**
 * Return a string with all " characters encoded.
 * {@code addslashes()} just quotes ALL special characters (including '), which is not suitable
 * for encoding a PHP string.
 */
function phpescapestring($s) {
  return str_replace("\"", "\\\"", $s);
}

function write_out_formatted_json($fp, $result) {
  $first = true;
  fwrite($fp, "{");
  foreach ($result as $key => $value) {
    if (!$first) {
      fwrite($fp, ",");
    }
    $first = false;
    fwrite($fp, "\n  " . json_encode($key) . ": " . json_encode($value));
  }
  fwrite($fp, "\n}\n");
}
