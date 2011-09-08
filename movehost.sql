## Update options and Links

SET @uri_from = '{oldurl}';
SET @uri_to = '{newurl}';
 
UPDATE wp_options SET `option_value` = REPLACE(`option_value`, @uri_from, @uri_to) 
	WHERE `option_value` LIKE CONCAT( "%", @uri_from, "%" );
 
UPDATE wp_posts SET `guid` = REPLACE(`guid`, @uri_from, @uri_to);
 
UPDATE wp_posts SET `post_content` = REPLACE(`post_content`, @uri_from, @uri_to);
 
UPDATE wp_postmeta SET `meta_value` = REPLACE(`meta_value`, @uri_from, @uri_to);


