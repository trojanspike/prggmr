# Time($time, [$vars = null])

Signal repeating events based on time passed in milliseconds.

## Parameters

### $time

The amount of time in milliseconds before triggering the signal.

### $vars
__Default :__ ```null```

Additional variables to pass the signal handler.

## Namespace

\prggmr\signal

## Description

The Interval signal triggers event based on time, it does this by accepting the required elasped time
and calculating the timestamp in the future it will signal.

The signal has a routine method that returns either the amount of time in milliseconds before it
is to signal or the signal dispatch response code once the required amount of time has passed.

Once signaled it will reset itself for future signaling based on the original interval time.

The signal can not be evaluated by the engine preventing it from being signaled directly.

This signal is in the API for the [timeout](../api/timeout.html) and [interval](../api/interval.html) functions.

## Example

    handle(function(){
        echo "1 second just passed";
    }, new \prggmr\signal\Interval(1000));

## Parent

Interval is a child of [Time](time.html).