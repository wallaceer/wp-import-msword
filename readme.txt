=== Wordpress Import content from Word document ===
Contributors: WiTech
Donate link: 
Tags: word, import, content, post, page
Requires at least: 6.0
Tested up to: 6.2
Stable tag: 1.2
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wpimportword

This plugin allow to generate WP post or page importing Word document.

== Description ==
This plugin allow to generate WP post or page importing Word document.
You can use .docx or .doc extension, but docx is preferred.
You can configure the plugin with this informations:
1. Directory to save documents (only directory name)

2. Post type (Page/Post)

3. Post status after creation (Publish, Draft, Pending)

4. Enable document parsing

5. Post parent mapping: it contains a json with data about the post that the system should have used to assign the parent post or page. Is it necessary that the content of the file word presente a mapped structure and the reference to this mapping
ex. {"T":"502","E":"504"}

6. Character separator for document parsing

7. String structure. The position of field in the structure define the position in the document. If empty this configuration will not evaluate.

8. String structure for ACF fields: map id acf field with acf name writed in String Structure. If empty this configuration will not evaluate.
You can configure the plugin in order that it manages the content in two different way
- 1. all content is inserted in the post content
- 2. the content is splitted in two subcontent inserted in nuto_parte_1 and nuto_parte_2 if theyr exists and are mapped into the json writed in ACF fields configuration section

9. Show only errors

10. Send report Email

11. Email address for report

== Screenshots ==

1. Upload files and generate content
2. Configuration
3. Logs
4. Documentation


== Changelog ==

= 1.0 =
* First full version with all functionalities

= 1.1 =
* Added some fix

= 1.2 =
* Added update manager


== Upgrade Notice ==

= 1.2 =
Inserted the plugin update checker