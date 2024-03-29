<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pipelines
    |--------------------------------------------------------------------------
    |
    | Define pipelines in this configuration file, each designed to channel data from
    | various sources to a specified target using a predefined schema. To implement
    | the required functionality for any pipeline, create a set of custom classes
    | that extends the provided base classes.
    | For simple situations use filenames with known extensions instead of classes.
    |
    */
    'pipelines' => [
        /*
         * name => [
         *   'sources' => [Source1::class, Source2::class],
         *   'schema' => Schema::class,
         *   'target' => Target::class
         * ];
         *
         */
    ],
    'spreadsheets' => [
        /*
         * key1 => spreadsheetId1,
         * key2 => spreadsheetId2,
         * key3 => spreadsheetId3,
         */
    ],
];
