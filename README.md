# jcr_section_custom

Adds up to five extra custom fields of up to 255 characters to the
[Presentation ›
Sections](http://docs.textpattern.io/administration/sections-panel)
panel along with corresponding tags to output the custom field and to
test if it contains a value or matches a specific value.

### Use cases

Use whenever extra information needs to be stored with a section. For
example:

-   Store a txp image ID number and use it to associate a cover image
    with the section.
-   Store associated details, for example the background colour or key
    colour of a section.
-   To use a different title for the menu than the section title.
-   To create links between parallel sections in different languages.
-   ...

## Installation / Deinstallation / Upgrading

### Installation

Paste the `.txt` installer code into the *Admin › Plugins* panel, or
upload the plugin's `.php` file via the *Upload plugin* button, then
install and enable the plugin.

### Upgrading

The plugin automatically migrates custom field data and the database
structure from the earlier single custom field variant (v0.1) to the new
format. No changes are needed to the public tags as the new default
settings correspond to the old tag. Nevertheless, it is always advisable
to make a database backup before upgrading.

### De-installation

The plugin cleans up after itself: deinstalling (deleting) the plugin
removes the extra columns from the database as well as custom field
names and labels. To stop using the plugin but keep the custom field
data in the database, just disable (deactivate) the plugin but don't
delete it.

## Plugin tags

### jcr_section_custom

Outputs the content of the section custom field.

#### Tag attributes

`name`\
Specifies the name of the section custom field.\
Example: Use `name="title_image"` to output the title_image custom
field. Default: jcr_sec_custom_1.

`escape`\
Escape HTML entities such as `<`, `>` and `&` prior to echoing the field
contents.\
Supports extended escape values in txp 4.8\
Example: Use `escape="textile"` to convert textile in the value.
Default: none.

`default`\
Specifies the default output if the custom field is empty\
Example: Use `default="123"` to output "123", e.g. for use as the
default image ID number. Default: empty.

`wraptag`\
Wrap the custom field contents in an HTML tag\
Example: Use `wraptag="h2"` to output `<h2>Custom field value</h2>`.
Default: empty.

`class`\
Specifies a class to be added to the `wraptag` attribute\
Example: Use `wraptag="p" class="intro"` to output
`<p class="intro">Custom field value</p>`. Default: empty

### jcr_if_section_custom

Tests for existence of a section custom field, or whether one or several
matches a value or pattern.

#### Tag attributes

`name`\
Specifies the name of the section custom field.\
Example: Use `name="title_image"` to output the title_image custom
field. Default: jcr_sec_custom_1.

`value`\
Value to test against (optional).\
If not specified, the tag tests for the existence of any value in the
specified section custom field.\
Example: Use `value="english"` to output only those sections whose
"language" section custom field is english. Default: none.

`match`\
Match testing: exact, any, all, pattern. See the docs for
[if_custom_field](https://docs.textpattern.com/tags/if_custom_field).\
Default: exact.

`separator`\
Item separator for match="any" or "all". Otherwise ignored.\
Default: empty.

## Examples

### Example 1

Outputs the specified title image from the image ID number. If no image
is specified a default image with image ID\# 123 is output:

    <txp:section_list>
      <txp:images id='<txp:jcr_section_custom name="title_image" escape="" default="123" />'>
         <txp:image />
      </txp:images>
    </txp:section_list>

where the section custom field is used to store the Image ID\# of the
title image.

### Example 2

Outputs a menu of only those sections whose "language" custom field
equals "english":

    <txp:section_list wraptag="ul" break="" class="nav en">
      <txp:jcr_if_section_custom name="language" value="english">
        <li><txp:section title link /></li>
      </txp:jcr_if_section_custom>
    </txp:section_list>

## Custom field labels

The label displayed alongside the custom field in the edit image panel
can be changed by specifying a new label using the *Install from
Textpack* field in the [Admin ›
Languages](http://docs.textpattern.io/administration/languages-panel.html)
panel. Enter your own information in the following pattern and click
**Upload**:

    #@owner jcr_section_custom
    #@language en, en-gb, en-us
    #@section
    jcr_sec_custom_1 => Your label
    jcr_sec_custom_2 => Your other label
    …

replacing `en` with your own language and `Your label` with your own
desired label.

## Changelog and credits

### Changelog

-   Version 0.2.3 -- 2020/12/18 -- No new functionality. Textpack fixes
    and align with other custom field plugins
-   Version 0.2.2 -- 2020/06/27 -- Handle migration from previous
    versions of the plugin on install
-   Version 0.2.1 -- 2020/06/27 -- Fix for missing custom_field name vs.
    missing value for cf
-   Version 0.2 -- 2020/03/04 -- Expand to handle multiple custom fields
-   Version 0.1 -- 2018/07/18 -- Remedy table not being created on
    install
-   Version 0.1 -- 2016/03/04 -- First release

### Credits

Robert Wetzlmayr's
[wet_profile](https://github.com/rwetzlmayr/wet_profile) plugin for the
starting point, and further examples by [Stef
Dawson](http://www.stefdawson.com) and [Jukka
Svahn](https://github.com/gocom).
