# ArrayContains($array, [$strict = false])

Signal an event based on an array contain lookup.

## Namespace

\prggmr\signal

## Description

The array contains signal uses the [array_search](http://php.net/array_search) function for 
performing the search.

If you need greater performance and can sort and compare the array try the [ArrayBinary](array_binary.html) signal.

## Example

    handle(function($value){
        echo "This used a array_search lookup";
        echo "Found $value";
    }, new \prggmr\signal\ArrayContains(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10)));

    signal(4);

## Methods

### __construct($array, [$cmp = null])

Constructs a new ArraySearch signal.

#### $array

Hackstack to search.

#### $strict
__Default :__ ```false```

Use strict comparison checks.

### evaluate($signal)

Evalutes the given signal to determine if it should trigger the signal.

#### $signal

Signal to evaluate

#### Returns

##### Boolean

Returns boolean ```false``` when evaluation fails.

##### Mixed

Returns the signal under evaluation when successful.

## Parent

ArrayContains is a child of [Complex](complex.html).