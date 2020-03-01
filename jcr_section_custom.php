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

$plugin['version'] = '0.1';
$plugin['author'] = 'jcr / txpbuilders';
$plugin['author_uri'] = 'http://txp.builders';
$plugin['description'] = 'Adds a custom field to the sections panel';

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

$plugin['flags'] = '';

// Plugin 'textpack' is optional. It provides i18n strings to be used in conjunction with gTxt().
// Syntax:
// ## arbitrary comment
// #@event
// #@language ISO-LANGUAGE-CODE
// abc_string_name => Localized String

$plugin['textpack'] = <<< EOT
#@admin
#@language en-gb
jcr_section_custom => Section Image ID
#@language de-de
jcr_section_custom => Rubrik Bild-ID
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
	}

	/**
   * Add and remove custom field from txp_file table.
   *
   * @param $event string
   * @param $step string  The lifecycle phase of this plugin
   */
  public static function lifecycle($event, $step)
  {
      switch ($step) {
          case 'enabled':
              break;
          case 'disabled':
              break;
          case 'installed':
              safe_alter(
                'txp_section',
                'ADD COLUMN jcr_section_custom VARCHAR(255) NULL AFTER title'
              );
              break;
          case 'deleted':
              safe_alter(
                'txp_file',
                'DROP COLUMN jcr_section_custom'
              );
              break;
      }
      return;
  }

	/**
	 * Paint additional fields for file custom field
	 *
	 * @param $event string
	 * @param $step string
	 * @param $dummy string
	 * @param $rs array The current file's data
	 * @return string
	 */
	public static function ui($event, $step, $dummy, $rs)
	{
		extract(lAtts(array(
			'jcr_section_custom' => ''
		), $rs, 0));

		return
			inputLabel('jcr_section_custom', fInput('text', 'jcr_section_custom', $jcr_section_custom, '', '', '', INPUT_REGULAR, '', 'jcr_section_custom'), 'jcr_section_custom').n;
	}

	/**
	 * Save additional file custom fields
	 *
	 * @param $event string
	 * @param $step string
	 */
	public static function save($event, $step)
	{
		extract(doSlash(psa(array('jcr_section_custom', 'name'))));

		safe_update('txp_section',
		  "jcr_section_custom = '$jcr_section_custom'",
			"name = '$name'"
		);
	}
}

if (txpinterface === 'admin') {

    new jcr_section_custom;

} elseif (txpinterface === 'public') {

    if (class_exists('\Textpattern\Tag\Registry')) {
        Txp::get('\Textpattern\Tag\Registry')
            ->register('jcr_section_custom');
    }

}

  /**
   * Public tag: Output custom section field
   * @param  string $atts[escape] Convert special characters to HTML entities.
   * @return string custom field output
   * <code>
   *        <txp:jcr_section_custom escape="html" />
   * </code>
   */

    function jcr_section_custom($atts)
    {
        global $thissection;

        assert_section();

        extract(lAtts(array(
            'escape' => 'html',
        ), $atts));

        return ($escape == 'html')
            ? txpspecialchars($thissection['jcr_section_custom'])
            : $thissection['jcr_section_custom'];
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

Adds a single extra custom field of up to 255 characters to the "Presentation › Sections":http://docs.textpattern.io/administration/sections-panel panel and provides a corresponding tag to output the custom field. 


h2. Use cases

Use whenever extra information needs to be stored with a section. For example:

* Store a txp image ID number and use it as a title image for a section.
* Store an image hex color to colour-code a section.
* …


h2(#installation). Installation

Paste the code into the  _Admin › Plugins_ panel, install and enable the plugin.


h2(#tags). Tags

bc. <txp:jcr_section_custom />

Outputs the content of the file custom field.

h3. Tag attributes

*escape*
Escape HTML entities such as @<@, @>@ and @&@ prior to echoing the field contents. 
Example: Use @escape=""@ to suppress conversion. Default: @html@.


h2(#examples). Example

Output a list of sections with respective title images:

bc. <txp:section_list wraptag="ul" break="li">
  <a href="<txp:section url="1" />" title="<txp:section title="1" />">
    <txp:image id='<txp:jcr_section_custom />' />
  </a>
</txp:section_list>

p. when the file custom field is used to store the Image ID# of the section title image.


h2. Changing the label of the custom field

The name of custom field can be changed by specifying a new label using the _Install from Textpack_ field in the "Admin › Languages":http://docs.textpattern.io/administration/languages-panel panel. Enter your own information in the following pattern and click *Upload*:

bc.. #@admin
#@language en-gb
jcr_section_custom => Your label

p. replacing @en-gb@ with your own language and @Your label@ with your own desired label.


h2(#deinstallation). De-installation

The plugin cleans up after itself: deinstalling the plugin removes the extra column from the database. To stop using the plugin but keep the database tables, just disable (deactivate) the plugin but don't delete it.


h2(#changelog). Changelog

h3. Version 0.1 – 2016/03/04

* First release


h2(#credits). Credits

Robert Wetzlmayr’s "wet_profile":https://github.com/rwetzlmayr/wet_profile plugin for the starting point, and further examples by "Stef Dawson":http://www.stefdawson.com and "Jukka Svahn":https://github.com/gocom.
# --- END PLUGIN HELP ---
-->
<?php
}
?>
