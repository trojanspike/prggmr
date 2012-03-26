# prggmr API

prggmr provides a global API as normal PHP functions.

The API functions are a mirror image of those in the engine, so once you
learn the global API you have essentially learned the engine's API.

## handle ($closure, $signal, [$priority = null, [$exhaust = 1]])

Creates a new signal handler.

### Return

Returns a new \prggmr\Handle object instance, which must be used for removing the handle from the signal at a later time if needed.

### Example

    handle(function(){
        echo "HelloWorld";
    }, 'helloworld')

### Description

A handle is how prggmr responds to signals, for each signal any number of handles can be registered, each with their own priority and exhaustion.

### $closure

All handles use Closures for their execution functions, any other type of callable is not allowed.

Note that a handle will automatically bind the current \prggmr\Event instance to the closure, allowing for the use of "$this". Any other binding automatically created by PHP will be voided.

### $signal

A handle will be attached to this signal, the signal can be any string, integer (reference the internal engine signals document for restricted integers) or a complex signal object.

Signals are the real magic behind prggmr and while using strings and integers will suit the needs of most applications, complex signals are also possible.

You can read more about <a href="http://en.wikipedia.org/wiki/Complex_event_processing">complex events</a>.

### $priority

The handle priority is the numeric rank it holds in the signal queue, whichever handle has the greatest priority will be executed first.

By default the priority works as a min-heap where 0 has the greatest rank.

### $exhaust

The exhaustion is the number of times a handle will execute when it's signal is called, by default this value is set to 1. 

Setting to null allows for inifite execution.

## handle_remove ($signal, $handle)

Removes a signal handle.

### Return

Void