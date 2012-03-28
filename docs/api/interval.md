# interval($function, $interval, [[$vars = null, $priority = QUEUE_DEFAULT_PRIORITY]])

Registers a function to execute each time the number of milliseconds passed.

## Return

Array

    [
        \prggmr\signal\Interval $signal,
        \prggmr\Handle $handle
    ]

## Example

    interval(function(){
        echo "1 second just passed";
    }, 1000);

## Description

```interval``` allows for registering functions that will execute after a certain amount of time has
passed in milliseconds and repeat until the interval is removed.

The interval API function is a shorthand convience function: 

    handle(function(){
        echo "1 second passed";
    }, new \prggmr\signal\Interval(1000));

The interval handle is a standard prggmr ```handle``` assigned to a ```\prggmr\signal\Interval``` signal,
which is a complex signal read more about the <a href="../signals/interval.html">interval signal</a>.

## Parameters

### $function

A ```Closure``` any other type of callable is not allowed.

Note that a handle will automatically bind the current ```\prggmr\Event``` instance to the closure, allowing for the use of ```$this```. 

Any other binding automatically created by PHP will be voided.

### $interval

The number of milliseconds between calling the function.

### $vars
__Default :__ null

An array of additional variables to pass the interval handle.

### $priority
__Default :__ QUEUE_DEFAULT_PRIORITY

The interval functions priority

Note that unless a interval is registered using the same ```\prggmr\signal\Interval``` signal intervals
that execute at the same time will have an unpredictable priority.

## Examples

### Timed signaling

This example will create an interval that calls the "helloworld" signal every second.

    interval(function(){
        echo "Calling the helloworld signal.";
    }, 1000);

    handle(function(){
        echo "HelloWorld";
    }, "helloworld");

#### Result

    -- 1 second passes
    Calling the helloworld signal
    HelloWorld
    -- 1 second passes
    Calling the helloworld signal
    HelloWorld
    -- 1 second passes
    Calling the helloworld signal
    HelloWorld
    -- 1 second passes
    Calling the helloworld signal
    HelloWorld
    ...