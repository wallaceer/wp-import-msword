This plugin creates a Page or Post in your Wordpress site by reading content from a word document.

Tested up to WP 6.2

**Requirements**
- PHP zip extension

**How it works**

This plugin allows to generate WP post or page importing Word document.
You can use .docx or .doc extension, but docx is preferred.
You can configure the plugin with this informations:
1. Directory to save documents (only directory name)

2. Post type (Page/Post)

3. Post status after creation (Publish, Draft, Pending)

4. Enable document parsing

4.1. Post parent mapping: it contains a json with data about the post that the system should have used to assign the parent post or page. Is it necessary that the content of the file word presente a mapped structure and the reference to this mapping
ex. {"T":"502","E":"504"}

4.2. Character separator for document parsing

4.3. String structure. The position of field in the structure define the position in the document. If empty this configuration will not evaluate.

4.4. String structure for ACF fields: map id acf field with acf name writed in String Structure. If empty this configuration will not evaluate.
You can configure the plugin in order that it manages the content in two different way
- 1. all content is inserted in the post content
- 2. the content is splitted in two subcontent inserted in nuto_parte_1 and nuto_parte_2 if theyr exists and are mapped into the json writed in ACF fields configuration section
ex. {"acf_tipologia_pagina":"field_636e53251c164","acf_immagine":"field_636e54df1c165","acf_macroarea":"field_636e54f71c166","acf_from":"field_636e55111c167","acf_to":"field_636e55291c168","acf_ar":"field_636e553b1c169","contenuto_parte_1":"field_641dabc9cca40","contenuto_parte_2":"field_641dabd9cca41","tratta_status":"field_641dbed084c48"}

5. Show only errors

6. Send report Email

7. Email address for report