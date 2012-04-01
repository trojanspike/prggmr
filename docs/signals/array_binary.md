# ArrayBinary($array, [$vars = null, [$cmp = null]])

Signal an event based on a binary array search.

## Parameters

### $time

Hackstack to search, note this must be sorted.

### $vars
__Default :__ ```null```

Additional variables to pass the signal handlers.

### $cmp
__Default :__ ```null```

Comparison function to use when performing the binary search.

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

## Parent

ArrayBinary is a child of [Complex](complex.html).