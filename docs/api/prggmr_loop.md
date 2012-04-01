# prggmr_loop([$ttr = null])

Starts the prggmr engine event loop.

## Return

Void

## Parameters

### $ttr
__Default :__ ```null```

Amount of time in milliseconds to run the engine.

## Example

    interval(function(){
        echo "The loop is running";
    }, 1000);

    prggmr_loop();

### Results

    The loop is running
    The loop is running
    The loop is running
    The loop is running
    ...

## Description

Read more about prggmr's [event loop](../event_loop.html).