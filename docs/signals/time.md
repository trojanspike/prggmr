# Time($time, [$vars = null])

Signal events based on time passed in milliseconds.

## Namespace

\prggmr\signal

## Description

The Time signal triggers event based on time, it does this by accepting the required elasped time
and calculating the timestamp in the future it will signal.

The signal has a routine method that returns either the amount of time in milliseconds before it
is to signal or the signal dispatch response code once the required amount of time has passed.

The signal can not be evaluated by the engine preventing it from being signaled directly.

This signal is in the API for the [timeout](../api/timeout.html) and [interval](../api/interval.html) functions.

## Example

    handle(function(){
        echo "1 second just passed";
    }, new \prggmr\signal\Time(1000));

## Methods

### __construct($time, [$vars = null]))

#### $time

The amount of time in milliseconds before triggering the signal.

#### $vars
__Default :__ ```null```

Additional variables to pass the signal handler.

### routine([$history = null])

Determines when to trigger the signal. Once the signal has been triggered.

Once the time has passed it will always return false.

#### $history
__Default :__ ```null```

The current event history.

#### Returns

Array

    [
        null|ENGINE_ROUTING_SIGNAL,
        null,(engine idle time)
    ]

Boolean

False will be returned once the timeout has been signaled.

## Parent

Time is a child of [Complex](complex.html).