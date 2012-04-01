# Range

Signals event based on a range of integers.

## Namespace

\prggmr\signal

## Description

This allows for triggering signals based on a range of integers.

It works by using a simple GTE and LTE comparison of the lower and upper range limits.

## Example

    handle(function($integer){
        echo "Signaled $integer";
    }, new \prggmr\signal\Range(100, 250));

## Methods

### __construct($min, $max)

Constructs a new Range signal.

#### $min

The lowest possible value in the range.

#### $max

The highest possible value in the range.

### evaluate($signal)

Evalutes the given signal to determine if it should trigger the signal.

#### $signal

Signal to evaluate

#### Returns

Boolean

Returns boolean ```false``` when evaluation fails.

Integer

The integer signal contained within the range.

## Parent

Range is a child of [Complex](complex.html).