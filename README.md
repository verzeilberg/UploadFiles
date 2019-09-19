# Upload files

## Description
A upload module to manage your uploads. You can upload files by type, size etc.



## How to use
put below code into your .gitmodules file and update your submodules:

[submodule "User"] path = module/User url = https://github.com/verzeilberg/Login.git branch = master

At moment of typing there are 2 branches master and xml-user. Xml-user has one extra permission added to the administrator role. My advise is to use the master version.

## Upload settings
For every upload you can use a specific upload settings. Place below settings in your 
congif/autoload folder in a file named (for example) upload-files.local.php:

<pre>
'filesUploadSettings' => [
        'default' => [
            'uploadFolder' => '',
            'uploadeFileSize' => '',
            'allowedFileExtensions' => [],
        ]
    ]
</pre>

Specify the files upload settings, in this case it is <b>default</b>
- Set the <b>uploadFolder</b>, for example 'data/files'
- Set the <b>uploadeFileSize</b>, for example 5000
- Set the <b>allowedFileExtensions</b> for example 
[
text/xml,
application/xml,
	image/jpeg
]

Must be specified like mime types.

When uploading a file use the <b>'uploadFile($file, $allowCopy = false, $settings = 'default')'</b> 
function and give the post array of the file, allow copy (true or false) and the settings you want to use from your 
 config file.