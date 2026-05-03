
#### Accessing Files

There are two ways to access files:

##### Extending Existing Classes
Create a class that inherits from one of the provided endpoint classes.
Each extended class must define in `$path` a path to the file.
The used disk can be overwritten in  `$disk` as well.

##### Using Filename Extensions
Define a file with a specific extension,
associated with the desired endpoint behavior.
The file is located at the  `sheet-base` $disk.



````php
// config/sheet-base.php
return [
    'pipelines' => [
        'sources' => [
            'directory/data.neon',
            MyEndpoints\MyData:class
        ],
        .....
    ]
];

````
and the class for it:
````php
// App\MyEndPoints\MyData
class MyData  extends EndpointWriteNeon
{
    public string $path = 'directory/data2.neon';
}
````

##### Extension and endpoints

The following extensions are automatically mapped to endpoints:

| extension | reader class | writer class |
|---|---|---|
| array | EndpointReadArray | EndpointWriteArray |
| csv | EndpointReadCsv | EndpointWriteCsv |
| json | - | EndpointWriteJson |
| lang | - | EndpointWriteLang |
| neon | EndpointReadNeon | EndpointWriteNeon |
| php | - | EndpointWritePhp |
| psv | EndpointReadPsv | EndpointWritePsv |
| tsv | EndpointReadTsv | EndpointWriteTsv |
| txt | EndpointReadTxt | EndpointWriteTxt |
| yaml | EndpointReadYaml | EndpointWriteYaml |
| yml | EndpointReadYml | EndpointWriteYml |

