# prggmr_shutdown()

Shutdown the prggmr engine event loop.

## Return

Void

## Example

    interval(function(){
        echo "The loop is running";
        prggmr_shutdown();
    }, 1000);

    prggmr_loop();

### Results

    The loop is running

## Description

Read more about prggmr's <a href="../event_loop.html">event loop</a>.