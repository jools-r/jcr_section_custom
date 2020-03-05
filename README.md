# jcr_section_custom

Adds up to five extra custom fields of to 255 characters to the [Presentation › Sections](http://docs.textpattern.io/administration/sections-panel) pane along with corresponding tags to output the custom field, and to test if it contains a value or matches a specific value. 


## Use cases

Use whenever extra information needs to be stored with a section. For example:

* Store a txp image ID number and use it to associate a cover image with the section.
* Store associated details, for example the background colour or key colour of a section.
* To use a different title for the menu than the section title.
* To create links between parallel sections in different languages.
* …


## Installation

Paste the code into the _Admin › Plugins_ panel, install and enable the plugin.


## Tags

`<txp:jcr_section_custom />`

Outputs the content of the section custom field.

### Tag attributes

**name**

Specifies the name of the section custom field. 
Example: Use `name="title_image"` to output the title_image custom field. Default: custom_field_1.

**escape**

Escape HTML entities such as `<`, `>` and `&` prior to echoing the field contents. 
Example: Use `escape=""` to suppress conversion. Default: `html`.

**default**

Specifies the default output if the custom field is empty
Example: Use `default="123"` to output "123", e.g. for use as the default image ID number. Default: empty.

**wraptag**

Wrap the custom field contents in an HTML tag
Example: Use `wraptag="h2"` to output `<h2>Custom field value</h2>`. Default: empty.

**class**

Specifies a class to be added to the `wraptag` attribure
Example: Use `wraptag="p" class="intro"` to output `<p class="intro">Custom field value</p>`. Default: empty

`<txp:jcr_if_section_custom />`

Tests for existence of a section custom field, or whether one or several matches a value.or pattern.

### Tag attributes

**name**

Specifies the name of the section custom field. 
Example: Use `name="title_image"` to output the title_image custom field. Default: jcr_sec_custom_1.

**value**

Value to test against (optional). 
If not specified, the tag tests for the existence of any value in the specified section custom field.
Example: Use `value="english"` to output only those sections whose “language” section custom field is english. Default: none.

**match**

Match testing: exact, any, all, pattern. See the docs for [if_custom_field](https://docs.textpattern.com/tags/if_custom_field).
Default: exact.

**separator**

Item separator for match="any" or "all". Otherwise ignored.
Default: empty.


## Example

1. Outputs the specified title image from the image ID number. If no image is specified a default image with image ID# 123 is output:

```
<txp:section_list>
  <txp:images id='<txp:jcr_section_custom name="title_image" escape="" default="123" />'>
     <txp:image />
  </txp:images>
</txp:section_list>
```

where the section custom field is used to store the Image ID# of the title image.

2. Outputs a menu of only those sections whose "language" custom field equals "english":

```
<txp:section_list wraptag="ul" break="" class="nav en">
  <txp:jcr_if_section_custom name="language" value="english">
    <li><txp:section title link /></li>
  </txp:jcr_if_section_custom>
</txp:section_list>
```

## Changing the label of the custom field

The name of custom field can be changed by specifying a new label using the _Install from Textpack_ field in the [Admin › Languages](http://docs.textpattern.io/administration/languages-panel) panel. Enter your own information in the following pattern and click *Upload*:

```
#@admin
#@language en-gb
jcr_sec_custom_1 => Your label
jcr_sec_custom_2 => Your other label
…
```

replacing `en-gb` with your own language and `Your label` with your own desired label.


## De-installation

The plugin cleans up after itself: deinstalling the plugin removes the extra column from the database. To stop using the plugin but keep the database tables, just disable (deactivate) the plugin but don't delete it.


## Changelog

* Version 0.2 – 2020/03/04 – Expand to handle multiple custom fields
* Version 0.15 – 2018/07/18 – Remedy table not being created on install 
* Version 0.1 – 2016/03/04 – First release


## Credits

Robert Wetzlmayr’s [wet_profile](https://github.com/rwetzlmayr/wet_profile) plugin for the starting point, and further examples by [Stef Dawson](http://www.stefdawson.com) and [Jukka Svahn](https://github.com/gocom).