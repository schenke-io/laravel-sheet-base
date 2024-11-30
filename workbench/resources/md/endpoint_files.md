
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

