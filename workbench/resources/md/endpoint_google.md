
#### Endpoint to read Google Sheets

First get a service account in the Google api console and download the json file.
Then add the key `GOOGLE_APPLICATION_CREDENTIALS` to the `.env` file and fill the
path to this file.
```bash
GOOGLE_APPLICATION_CREDENTIALS=directory/google/file.json
```
Create an empty Google sheets document and share it with the email
from the service account.<br>

There are two ways to configure the spreadsheet ID used by this plugin:
- directly in your EndpointClass
- in the config file (recommended for clarity and multiple files)

In both cases you fill the worksheet name in `$sheetName` and
get than the URL of the Google sheet.
The spreadsheet ID is the part of the URL after `/d/` and
before `/edit`. For example, in the
URL `https://docs.google.com/spreadsheets/d/123ABC-xyz123/edit`, the spreadsheet ID is 123ABC-xyz123.

In `$spreadsheetId` you enter either the ID itself or the name of the
key in `config/sheet-base.php` as shown below.

```php
class GoogleSheetLang extends EndpointReadGoogleSheet
{
    public string $spreadsheetId = '1ttfjdfdjfdfjdfdjfdfdkfdfdQkGDE';
    // or: public string $spreadsheetId = 'File-Main';
    public string $sheetName = 'Sheet1';
}

// in config/sheet-base.php
 
    'pipelines' => [
    .....
    ],
    'spreadsheets' => [
        'File-Main' => '1ttfjdfdjfdfjdfdjfdfdkfdfdQkGDE'
    ]        
  
  

```
The first row in the spreadsheet must contain the column names, as specified in the pipeline schema. 
The table width is determined by the number of columns with header values. 
Additional headers to the right are ignored. 
Data reading stops when the first column is empty, and subsequent rows are discarded.

