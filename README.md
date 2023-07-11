This plugin create a Page or a Post in your Wordpress site by reading content from a word document.

Tested up to WP 6.2

**Requirements**
- PHP zip extension

**How it works**
This plugin you permit tu generate post or page in WP directly importing content from Word document.
You can use .docx or .doc extension, but the preferred format is DOCX; docx is the more tested format.
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
