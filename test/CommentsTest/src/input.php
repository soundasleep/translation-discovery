<?php

echo t("Hello");

/**
 * echo t("World");
 */

// echo t("World");

/**
 * echo "World" /* i18n */ /*
 */

// echo "World" /* i18n */;

// but we will still find non-phpdoc comments

/*
 * echo t("Cat");
 */

?>

<a href="https://groups.google.com/group/<?php echo htmlspecialchars(get_site_config('google_groups_announce')); ?>" target="_blank" class="visit"><?php echo ht("Visit this group"); ?></a>
