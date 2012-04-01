# ArrayBinary

Signal an event based on a binary array search.

## Namespace

\prggmr\signal

## Description

The binary search signal is designed to allow for large scale array lookups.

## Example

    handle(function($value){
        echo "This used a binary array search";
        echo "Found $value";
    }, new \prggmr\signal\ArrayBinary(array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10)));

    signal(4);

## Methods

### __construct($array, [$cmp = null])

Constructs a new ArrayBinary signal.

#### $array

Hackstack to search, note this must be sorted.

#### $cmp
__Default :__ ```null```

Comparison function to use when performing the binary search.

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

ArrayBinary is a child of [Complex](complex.html).