# timeout ($function, $timeout, [[$vars = null, $priority = QUEUE_DEFAULT_PRIORITY]])

Registers the provided function to execute after the timeout in milliseconds.

## Return

Array

    [
        \prggmr\signal\Time $signal,
        \prggmr\Handle $handle
    ]

## Example

    timeout(function(){
        echo "1 second just passed";
    }, 1000);

## Parameters

### $function

A ```Closure``` any other PHP callable is not allowed.

### $timeout

The number of milliseconds before calling the function.