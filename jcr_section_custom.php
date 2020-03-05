<?php

// This is a PLUGIN TEMPLATE for Textpattern CMS.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Plugin names should start with a three letter prefix which is
// unique and reserved for each plugin author ("abc" is just an example).
// Uncomment and edit this line to override:
$plugin['name'] = 'jcr_section_custom';

// Allow raw HTML help, as opposed to Textile.
// 0 = Plugin help is in Textile format, no raw HTML allowed (default).
// 1 = Plugin help is in raw HTML.  Not recommended.
# $plugin['allow_html_help'] = 1;

$plugin['version'] = '0.15';
$plugin['author'] = 'jcr / txpbuilders';
$plugin['author_uri'] = 'http://txp.builders';
$plugin['description'] = 'Adds multiple custom fields to the sections panel';

// Plugin load order:
// The default value of 5 would fit most plugins, while for instance comment
// spam evaluators or URL redirectors would probably want to run earlier
// (1...4) to prepare the environment for everything else that follows.
// Values 6...9 should be considered for plugins which would work late.
// This order is user-overrideable.
$plugin['order'] = '5';

// Plugin 'type' defines where the plugin is loaded
// 0 = public              : only on the public side of the website (default)
// 1 = public+admin        : on both the public and admin side
// 2 = library             : only when include_plugin() or require_plugin() is called
// 3 = admin               : only on the admin side (no AJAX)
// 4 = admin+ajax          : only on the admin side (AJAX supported)
// 5 = public+admin+ajax   : on both the public and admin side (AJAX supported)
$plugin['type'] = '1';

// Plugin "flags" signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

$plugin['flags'] = '3';

// Plugin 'textpack' is optional. It provides i18n strings to be used in conjunction with gTxt().
// Syntax:
// ## arbitrary comment
// #@event
// #@language ISO-LANGUAGE-CODE
// abc_string_name => Localized String

$plugin['textpack'] = <<< EOT
#@admin
#@language en-gb
jcr_section_custom => Section custom fields
jcr_sec_custom_1 => Hero image
jcr_sec_custom_2 => Menu title
jcr_sec_custom_3 => Page title
jcr_sec_custom_4 => Accent color
jcr_sec_custom_5 => Background color
#@language de-de
jcr_section_custom => Sektion Custom-Felder
jcr_sec_custom_1 => Titelbild
jcr_sec_custom_2 => Menüname
jcr_sec_custom_3 => Seitentitel
jcr_sec_custom_4 => Akzentfarbe
jcr_sec_custom_5 => Hintergrundfarbe
EOT;

// End of textpack

if (!defined('txpinterface'))
		@include_once('zem_tpl.php');

# --- BEGIN PLUGIN CODE ---
class jcr_section_custom
{
	/**
	 * Initialise.
	 */
	function __construct()
	{
		// Hook into the system's callbacks.
		register_callback(array(__CLASS__, 'lifecycle'), 'plugin_lifecycle.jcr_section_custom');
		register_callback(array(__CLASS__, 'ui'), 'section_ui', 'extend_detail_form');
		register_callback(array(__CLASS__, 'save'), 'section', 'section_save');

		// Prefs pane for custom fields
		add_privs('prefs.jcr_section_custom', '1');

		// Redirect 'Options' link on plugins panel to preferences pane
		add_privs('plugin_prefs.jcr_section_custom', '1');
		register_callback(array(__CLASS__, 'options_prefs_redirect'), 'plugin_prefs.jcr_section_custom');
	}

	/**
	 * Add and remove custom field from txp_section table.
	 *
	 * @param $event string
	 * @param $step string  The lifecycle phase of this plugin
	 */
	public static function lifecycle($event, $step)
	{
		switch ($step) {
			case 'enabled':
				add_privs('prefs.jcr_section_custom', '1');
				break;
			case 'disabled':
				break;
			case 'installed':
				// Add section custom fields to txp_section table
				safe_alter(
					'txp_section',
					'ADD COLUMN jcr_sec_custom_1 VARCHAR(255) NULL AFTER title,
					 ADD COLUMN jcr_sec_custom_2 VARCHAR(255) NULL AFTER jcr_sec_custom_1,
					 ADD COLUMN jcr_sec_custom_3 VARCHAR(255) NULL AFTER jcr_sec_custom_2,
					 ADD COLUMN jcr_sec_custom_4 VARCHAR(255) NULL AFTER jcr_sec_custom_3,
					 ADD COLUMN jcr_sec_custom_5 VARCHAR(255) NULL AFTER jcr_sec_custom_4'
				);
				// Add prefs for section custom field names
				create_pref("section_custom_1_set", "", "jcr_section_custom", "0", "section_custom_set", "1");
				create_pref("section_custom_2_set", "", "jcr_section_custom", "0", "section_custom_set", "2");
				create_pref("section_custom_3_set", "", "jcr_section_custom", "0", "section_custom_set", "3");
				create_pref("section_custom_4_set", "", "jcr_section_custom", "0", "section_custom_set", "4");
				create_pref("section_custom_5_set", "", "jcr_section_custom", "0", "section_custom_set", "5");
				break;
			case 'deleted':
				// Remove columns from section table
				safe_alter(
					'txp_section',
					'DROP COLUMN jcr_sec_custom_1,
					 DROP COLUMN jcr_sec_custom_2,
					 DROP COLUMN jcr_sec_custom_3,
					 DROP COLUMN jcr_sec_custom_4,
					 DROP COLUMN jcr_sec_custom_5'
				);
				// Remove all prefs from event 'jcr_section_custom'.
				remove_pref(null,"jcr_section_custom");
				break;
		}
		return;
	}

	/**
	 * Paint additional fields for section custom field
	 *
	 * @param $event string
	 * @param $step string
	 * @param $dummy string
	 * @param $rs array The current section's data
	 * @return string
	 */
	public static function ui($event, $step, $dummy, $rs)
	{
		global $prefs;

		extract(lAtts(array(
			'jcr_sec_custom_1' => '',
			'jcr_sec_custom_2' => '',
			'jcr_sec_custom_3' => '',
			'jcr_sec_custom_4' => '',
			'jcr_sec_custom_5' => ''
		), $rs, 0));

		$out = "";

		$cfs = preg_grep('/^section_custom_\d+_set/', array_keys($prefs));

		foreach ($cfs as $name) {

			preg_match('/(\d+)/', $name, $match);

			if ($prefs[$name] !== '') {
				$out .= inputLabel('jcr_sec_custom_'.$match[1], fInput('text', 'jcr_sec_custom_'.$match[1], ${'jcr_sec_custom_'.$match[1]}, '', '', '', INPUT_REGULAR, '', 'jcr_sec_custom_'.$match[1]), 'jcr_sec_custom_'.$match[1]).n;
			}
		}

		return $out;
	}

	/**
	 * Save additional section custom fields
	 *
	 * @param $event string
	 * @param $step string
	 */
	public static function save($event, $step)
	{
		extract(doSlash(psa(array('jcr_sec_custom_1', 'jcr_sec_custom_2', 'jcr_sec_custom_3', 'jcr_sec_custom_4', 'jcr_sec_custom_5', 'name'))));
		$name = assert_string($name);
		safe_update('txp_section',"
			jcr_sec_custom_1 = '$jcr_sec_custom_1',
			jcr_sec_custom_2 = '$jcr_sec_custom_2',
			jcr_sec_custom_3 = '$jcr_sec_custom_3',
			jcr_sec_custom_4 = '$jcr_sec_custom_4',
			jcr_sec_custom_5 = '$jcr_sec_custom_5'",
			"name = '$name'"
		);
	}

	/**
	 * Renders a HTML section custom field.
	 *
	 * Can be altered by plugins via the 'prefs_ui > section_custom_set'
	 * pluggable UI callback event.
	 *
	 * @param  string $name HTML name of the widget
	 * @param  string $val  Initial (or current) content
	 * @return string HTML
	 * @todo   deprecate or move this when CFs are migrated to the meta store
	 */
	public static function section_custom_set($name, $val)
	{
		return pluggable_ui('prefs_ui', 'section_custom_set', text_input($name, $val, INPUT_REGULAR), $name, $val);
	}

	/**
	 * Re-route 'Options' link on Plugins panel to Admin › Preferences panel
	 *
	 */
	public static function options_prefs_redirect()
	{
		header("Location: index.php?event=prefs#prefs_group_jcr_section_custom");
	}

}

if (txpinterface === 'admin') {

	new jcr_section_custom;

} elseif (txpinterface === 'public') {

	if (class_exists('\Textpattern\Tag\Registry')) {
		Txp::get('\Textpattern\Tag\Registry')
			->register('jcr_section_custom')
			->register('jcr_if_section_custom');
	}

}

/**
 * Gets a list of section custom fields.
 *
 * @return  array
 */
function jcr_get_section_custom_fields()
{
	global $prefs;
	static $out = null;
	// Have cache?
	if (!is_array($out)) {
		$cfs = preg_grep('/^section_custom_\d+_set/', array_keys($prefs));
		$out = array();
		foreach ($cfs as $name) {
			preg_match('/(\d+)/', $name, $match);
			if ($prefs[$name] !== '') {
				$out[$match[1]] = strtolower($prefs[$name]);
			}
		}
	}
	return $out;
}

/**
 * Maps 'txp_section' table's columns to article data values.
 *
 * This function returns an array of 'data-value' => 'column' pairs.
 *
 * @return array
 */
function jcr_section_column_map()
{
	$section_custom = jcr_get_section_custom_fields();
	$section_custom_map = array();

	if ($section_custom) {
		foreach ($section_custom as $i => $name) {
			$section_custom_map[$name] = 'jcr_sec_custom_'.$i;
		}
	}

	return $section_custom_map;
}

/**
 * Public tag: Output custom section field
 * @param  string $atts[name] Name of custom field.
 * @param  string $atts[escape] Convert special characters to HTML entities.
 * @param  string $atts[default] Default output if field is empty.
 * @return string custom field output
 * <code>
 *        <txp:jcr_section_custom name="title_image" escape="html" />
 * </code>
 */
function jcr_section_custom($atts)
{
	global $thissection, $pretext;

	// If not currently in section context, get current section from pretext
	$current_section = empty($thissection) ? $pretext['s'] : $thissection['name'];

	extract(lAtts(array(
		'name'    => get_pref('section_custom_1_set'),
		'escape'  => 'html',
		'default' => '',
	), $atts));

	$name = strtolower($name);

	if ($rs = safe_rows_start("*",
		'txp_section',
		"name = '".$current_section."'"
	)) {
		while ($row = nextRow($rs)) {
			// Populate section custom field data;
			foreach (jcr_section_column_map() as $key => $column) {
				$currentsection[$key] = isset($row[$column]) ? $row[$column] : null;
			}
		}
	}

	if (!isset($currentsection[$name])) {
		trigger_error(gTxt('field_not_found', array('{name}' => $name)), E_USER_NOTICE);
		return '';
	}

	if (!isset($thing)) {
		$thing = $currentsection[$name] !== '' ? $currentsection[$name] : $default;
	}

	if ($escape === null) {
		if(function_exists('txp_escape')) {
			$thing = txp_escape(array('escape' => $escape), $thing);
		} else {
			$thing = txpspecialchars($thing);
		}    
	} else {
		$thing = parse($thing);
	}

	return doTag($thing, $wraptag, $class);
}

/**
 * Public tag: Output custom section field
 * @param  string $atts[name]    Name of custom field.
 * @param  string $atts[value]   Value to test against (optional).
 * @param  string $atts[match]   Match testing: esact, any, all, pattern.
 * @param  string $atts[separator] Item separator for match="any" or "all". Otherwise ignored.
 * @return string custom field output
 * <code>
 *        <txp:jcr_if_section_custom name="menu_title" /> … <txp:else /> … </txp:jcr_if_section_custom>
 * </code>
 */
function jcr_if_section_custom($atts, $thing = null)
{
	global $thissection, $pretext;

	// If not currently in section context, get current section from pretext
	$current_section = empty($thissection) ? $pretext['s'] : $thissection['name'];

	extract($atts = lAtts(array(
		'name'      => get_pref('section_custom_1_set'),
		'value'     => null,
		'match'     => 'exact',
		'separator' => '',
	), $atts));

	$name = strtolower($name);

	if ($rs = safe_rows_start("*",
		'txp_section',
		"name = '".$current_section."'"
	)) {
		while ($row = nextRow($rs)) {
			// Populate section custom field data;
			foreach (jcr_section_column_map() as $key => $column) {
				$currentsection[$key] = isset($row[$column]) ? $row[$column] : null;
			}
		}
	}

	if (!isset($currentsection[$name])) {
		trigger_error(gTxt('field_not_found', array('{name}' => $name)), E_USER_NOTICE);
		return '';
	}

	if ($value !== null) {
		$cond = txp_match($atts, $currentsection[$name]);
	} else {
		$cond = ($currentsection[$name] !== '');
	}

	return isset($thing) ? parse($thing, !empty($cond)) : !empty($cond);
}
# --- END PLUGIN CODE ---
if (0) {
?>
<!--
# --- BEGIN PLUGIN CSS ---

# --- END PLUGIN CSS ---
-->
<!--
# --- BEGIN PLUGIN HELP ---
h1. jcr_section_custom

Adds up to five extra custom fields of up to 255 characters to the "Presentation › Sections":http://docs.textpattern.io/administration/sections-panel panel along with  corresponding tags to output the custom field.and to test if it contains a value or matches a specific value.


h3. Use cases

Use whenever extra information needs to be stored with a section. For example:

* Store a txp image ID number and use it to associate a cover image with the section.
* Store associated details, for example the background colour or key colour of a section.
* To use a different title for the menu than the section title.
* To create links between parallel sections in different languages.
* …


h2(#installation). Installation

Paste the code into the  _Admin › Plugins_ panel, install and enable the plugin.


h2(#tags). Tags + Examples

h3. txp:jcr_section_custom

Outputs the content of the section custom field.

h4. Tag attributes

@name@
Specifies the name of the section custom field. 
Example: Use @name="title_image"@ to output the title_image custom field. Default: jcr_sec_custom_1.

@escape@
Escape HTML entities such as @<@, @>@ and @&@ prior to echoing the field contents. 
Supports extended escape values in txp 4.8
Example: Use @escape="textile"@ to convert textile in the value. Default: none.

@default@
Specifies the default output if the custom field is empty
Example: Use @default="123"@ to output "123", e.g. for use as the default image ID number. Default: empty.

@wraptag@
Wrap the custom field contents in an HTML tag
Example: Use @wraptag="h2"@ to output @<h2>Custom field value</h2>@. Default: empty.

@class@
Specifies a class to be added to the @wraptag@ attribure
Example: Use @wraptag="p" class="intro"@ to output @<p class="intro">Custom field value</p>@. Default: empty

h3. txp:jcr_if_section_custom

Tests for existence of a section custom field, or whether one or several matches a value.or pattern.

h4. Tag attributes

@name@
Specifies the name of the section custom field. 
Example: Use @name="title_image"@ to output the title_image custom field. Default: jcr_sec_custom_1.

@value@
Value to test against (optional). 
If not specified, the tag tests for the existence of any value in the specified section custom field.
Example: Use @value="english"@ to output only those sections whose “language” section custom field is english. Default: none.

@match@
Match testing: esact, any, all, pattern. See the docs for “if_custom_field”:https://docs.textpattern.com/tags/if_custom_field.
Default: exact.

@separator@
Item separator for match="any" or "all". Otherwise ignored
Default: empty.


h3. Example

1. Outputs the specified title image from the image ID number. If no image is specified a default image with image ID# 123 is output:

bc. <txp:section_list>
  <txp:images id='<txp:jcr_section_custom name="title_image" escape="" default="123" />'>
     <txp:image />
  </txp:images>
</txp:section_list>

p. where the section custom field is used to store the Image ID# of the title image.

2. Outputs a menu of only those sections whose "language" custom field equals "english":

bc. <txp:section_list wraptag="ul" break="" class="nav en">
  <txp:jcr_if_section_custom name="language" value="english">
    <li><txp:section title link /></li>
  </txp:jcr_if_section_custom>
</txp:section_list>


h2(#label). Changing the label of the custom field

The name of custom field can be changed by specifying a new label using the _Install from Textpack_ field in the "Admin › Languages":http://docs.textpattern.io/administration/languages-panel panel. Enter your own information in the following pattern and click *Upload*:

bc.. #@admin
#@language en-gb
jcr_sec_custom_1 => Your label
jcr_sec_custom_2 => Your other label
…

p. replacing @en-gb@ with your own language and @Your label@ with your own desired label.


h2(#deinstallation). De-installation

The plugin cleans up after itself: deinstalling the plugin removes the extra column from the database. To stop using the plugin but keep the database tables, just disable (deactivate) the plugin but don't delete it.


h2(#changelog). Changelog + Credits

h3. Version 0.1 – 2018/07/18

* Remedy table not being created on install 

h3. Version 0.1 – 2016/03/04

* First release
h3. changelog


h3. Credits

Robert Wetzlmayr’s "wet_profile":https://github.com/rwetzlmayr/wet_profile plugin for the starting point, and further examples by "Stef Dawson":http://www.stefdawson.com and "Jukka Svahn":https://github.com/gocom.
# --- END PLUGIN HELP ---
-->
<?php
}
?>
