# jcr_section_custom

Adds a single extra custom field of up to 255 characters to the [Presentation › Sections](http://docs.textpattern.io/administration/sections-panel) panel and provides a corresponding tag to output the custom field. 


## Use cases

Use whenever extra information needs to be stored with a section. For example:

* Store a txp image ID number and use it as a title image for a section.
* Store an image hex color to colour code a section.
* …


## Installation

Paste the code into the  _Admin › Plugins_ panel, install and enable the plugin.


## Tags

`<txp:jcr_section_custom />`

Outputs the content of the section custom field.

### Tag attributes

**escape**

Escape HTML entities such as `<`, `>` and `&` prior to echoing the field contents. 
Example: Use `escape=""` to suppress conversion. Default: `html`.


## Example

Output a list of sections with respective title images:

```
<txp:section_list wraptag="ul" break="li">
  <a href="<txp:section url="1" />" title="<txp:section title="1" />">
    <txp:image id='<txp:jcr_section_custom />' />
  </a>
</txp:section_list>
```

when the section custom field is used to store the Image ID# of the section title image.


## Changing the label of the custom field

The name of custom field can be changed by specifying a new label using the _Install from Textpack_ field in the [Admin › Languages](http://docs.textpattern.io/administration/languages-panel) panel. Enter your own information in the following pattern and click *Upload*:

```
#@admin
#@language en-gb
jcr_section_custom => Your label
```

replacing @en-gb@ with your own language and `Your label` with your own desired label.


## De-installation

The plugin cleans up after itself: deinstalling the plugin removes the extra column from the database. To stop using the plugin but keep the database tables, just disable (deactivate) the plugin but don't delete it.


## Changelog

* Version 0.1 – 2016/03/04 – First release


## Credits

Robert Wetzlmayr’s [wet_profile](https://github.com/rwetzlmayr/wet_profile) plugin for the starting point, and further examples by [Stef Dawson](http://www.stefdawson.com) and [Jukka Svahn](https://github.com/gocom).
