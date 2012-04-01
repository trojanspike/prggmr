# timeout($function, $timeout, [[$vars = null, $priority = QUEUE_DEFAULT_PRIORITY]])

Registers a function to execute as a timeout after a number of milliseconds.

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

## Description

```timeout``` allows for registering functions that will execute after a certain amount of time has
passed in milliseconds.

The timeout API function is a shorthand convience function: 

    handle(function(){
        echo "1 second passed";
    }, new \prggmr\signal\Time(1000));

The timeout handle is a standard prggmr ```handle``` assigned to a ```\prggmr\signal\Time``` signal,
which is a complex signal read more about the [time signal](../signals/time.html).

## Parameters

### $function

A ```Closure``` any other type of callable is not allowed.

Note that a handle will automatically bind the current ```\prggmr\Event``` instance to the closure, allowing for the use of ```$this```. 

Any other binding automatically created by PHP will be voided.

### $timeout

The number of milliseconds before calling the function.

### $vars
__Default :__ null

An array of additional variables to pass the timeout handle.

### $priority
__Default :__ QUEUE_DEFAULT_PRIORITY

The timeout functions priority

Note that unless a timeout is registered using the same ```\prggmr\signal\Time``` signal timeouts
that execute at the same time will have an unpredictable priority.

## Examples

### Timed signaling

This example will create a timeout that calls the "helloworld" signal after once second.

    timeout(function(){
        echo "Calling the helloworld signal.";
    }, 1000);

    handle(function(){
        echo "HelloWorld";
    }, "helloworld");

#### Result

    -- 1 second passes
    Calling the hellworld signal
    HelloWorld