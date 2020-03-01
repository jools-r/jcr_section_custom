# jcr_section_custom

Adds multiple extra custom fields of up to 255 characters to the [Presentation › Sections](http://docs.textpattern.io/administration/sections-panel) panel and provides a corresponding tag to output the custom field. 


## Use cases

Use whenever extra information needs to be stored with a section. For example:

* Store a txp image ID number and use it to associate a cover image with the section.
* Store associated details, for example the background colour or key colour of a section.
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


## Example

Outputs the specified title image from the image ID number. If no image is specified a default image with image ID# 123 is output:

```
<txp:section_list>
  <txp:images id='<txp:jcr_section_custom name="title_image" escape="" default="123" />'>
     <txp:image />
  </txp:images>
</txp:section_list>
```

where the section custom field is used to store the Image ID# of the title image.


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

* Version 0.15 – 2018/07/18 – Remedy table not being created on install 
* Version 0.1 – 2016/03/04 – First release


## Credits

Robert Wetzlmayr’s [wet_profile](https://github.com/rwetzlmayr/wet_profile) plugin for the starting point, and further examples by [Stef Dawson](http://www.stefdawson.com) and [Jukka Svahn](https://github.com/gocom).