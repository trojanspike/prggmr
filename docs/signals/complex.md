# Complex(void)

Base class for ***all*** complex signals.

If you are writing a complex signal you ***must*** inherit this class, otherwise
the engine will not know it is complex.

## Parameters

### $time

Hackstack to search.

### $strict
__Default :__ ```false```

Use strict comparison checks.

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

## Parent

ArrayContains is a child of [Complex](complex.html).