# handle($closure, $signal, [$priority = null, [$exhaust = 1]])

Creates a new signal handler.

## Return

Returns a new ```\prggmr\Handle``` instance.

## Example

    handle(function(){
        echo "HelloWorld";
    }, 'helloworld')

## Description

A handle is how prggmr responds to signals, for each signal any number of handles can be registered, each with their own priority and exhaustion.

## Parameters

### $closure

A ```Closure``` any other type of callable is not allowed.

Note that a handle will automatically bind the current ```\prggmr\Event``` instance to the closure, allowing for the use of ```$this```. 

Any other binding automatically created by PHP will be voided.

### $signal

A handle will be attached to this signal, the signal can be any string, integer or complex signal object.

Signals are the real magic behind prggmr which allow for CEP, read more about <a href="signals.html">signals</a>.

### $priority
__Default :__ 100

The handle priority is the numeric rank it holds in the signal queue, whichever handle has the greatest priority will be executed first.

By default the priority works as a min-heap where 0 has the greatest rank.

### $exhaust
__Default :__ 1

The number of times a signal call will execute the handle.

Setting to null allows for infinite execution.

## Examples

### Complex Signals

Using complex signals handles can be registered based on complex algorithms, comparisons, time, socket connections or 
any other computation you can think of.

Here is an example using the ```\prggmr\signal\Query``` signal.

    handle(function($user, $action, $amount){
        echo "$user just performed $action for $amount points";
    }, new \prggmr\signal\Query(":user/:action/:amount"), null, null);

    signal("nick/attack/50");
    signal("izzi/dodge/25");
    signal("nick/falldown/100");

#### Result

    nick just performed attack for 50 points
    izzi just performed dodge for 25 points
    nick just performed falldown for 100 points

### Multiple handles and priority

When the priority is set handles with the lowest priority will execute first, otherwise handles will
execute in LIFO.

    handle(function(){
        echo $this->hello.$this->world;
    }, "helloworld", 12);

    handle(function(){
        $this->hello = "Hello";
    }, "helloworld", 10);

    handle(function(){
        $this->world = "World";
    }, "helloworld", 11);

    signal("helloworld");

#### Result

    HelloWorld

### Exhaustion rates

You can change the number of times a handle will execute by providing the ```$exhaust``` parameter.

    handle(function(){
        echo "I executed once";
    }, 'exhaust');

    handle(function(){
        echo "I executed twice";
    }, 'exhaust', null, 2);

    signal('exhaust');
    signal('exhaust');

#### Result

    I executed once
    I executed twice
    I executed twice

Setting the exhaust of ```null``` will create a handle that never exhausts.

    handle(function(){
        echo "HelloWorld_1";
    }, "helloworld");

    handle(function(){
        echo "HelloWorld_2";
    }, "helloworld", null, null);

    while(true) {
        signal('helloworld');
    }

#### Result
    
    HelloWorld_2
    HelloWorld_1
    HelloWorld_2
    HelloWorld_2
    HelloWorld_2
    HelloWorld_2
    HelloWorld_2
    ...