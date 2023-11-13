=== Wordpress Import content from Word document ===
Contributors: Walter Santi
Tags: word, import, content, post, page
Requires at least: 5.2
Tested up to: 6.4.1
Stable tag: 2.3.2
License:
License URI:
Text Domain: wpimportword

== Description ==
Whit this plugin you can create a Page or Post in your Wordpress site reading content from a word document.
You can use .docx or .doc extension, but the preferred format is DOCX; docx is the more tested format.
The plugin has four section in the WP backend
- Word Import, used for importing documents and create contents
- Configuration, for configuring plugin
- Logs, for reading import register
- Help, for reading this guide

Tested up to WP 6.4.1

**Requirements**
- PHP zip extension

**How it works**
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
   1. all content is inserted in the post content
   2. the content is splitted in two subcontent inserted in nuto_parte_1 and nuto_parte_2 if theyr exists and are mapped into the json writed in ACF fields configuration section

9. Show only errors

10. Send report Email

11. Email address for report 

When you have configured the plugin you have to go to the import section "Word Import" and
1. Load the documents
2. Click on "Upload"
3. Read the message results or the log in case of errors
4. Verify the contents created
5. Edit the contents created


== Changelog ==

= 2.0 =
* Moved to ZipArchive()

= 2.1 =
* Code optimization

= 2.2 =
* Code optimization

= 2.3 =
* Refined guide, no code variations

= 2.3.1 =
* Tested with WP 6.3.1 and PHP 8.0.29

= 2.3.2 =
* Tested with WP 6.4.1