
#### Endpoint to write language files

One pipeline can be used to write language php files.
You need a special schema  and target file.
````php
// App\MyEndpoints\LanguageSchema
class LanguageSchema extends SheetBaseSchema 
{
    protected function define(): void
    {
        $this->addDot('key');  // must be first, name can be different 
        $this->addLanguage('de');  // only language codes as column names
        $this->addLanguage('en');
    }
}

// App\MyEndpoints\LanguageTarget
class LanguageTarget extends EndpointWriteLang
{
    // were to write the language files
    public string $root = '/'; 
    // which first parts of the dot-keys should result in files
    public array $fileBases = ['home']; 
}


````