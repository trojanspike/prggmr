# signal($signal, [$vars = null, [$event = null]])

Signals an event.

## Return

```\prggmr\Event``` instance representing this signal.

## Example

    signal('helloworld');
    signal(11292012);

## Description

A signal is what triggers an event and executes any assigned handles.

See [signals](../signals.html) for signals which are restricted for use and should
never be fired directly.

## Parameters

### $signal

This will be a string, integer or ```\prggmr\Signal``` instance. 

You should never signal a ```\prggmr\signal\Complex``` instance directly.

### $vars
__Default :__ null

You can pass signal handles variables by providing an array of variables.

### $event
__Default :__ null

If needed you can directly provide a ```\prggmr\Event``` instance to signal handles.

## Examples

### Invalid signals

When an invalid signal is provided a ```\prggmr\EngineException``` exception or ```\prggmr\engine\Signals::INVALID_SIGNAL``` signal
will be triggered dependent on the ```ENGINE_EXCEPTIONS``` configuration value, by default an exceptions are enabled.

    signal([]);

#### Result

    Fatal error: Uncaught exception 'prggmr\EngineException' with message 'Invalid or unknown signal' in /prggmr/src/engine.php:193
    Stack trace:
    #0 [internal function]: prggmr\Engine->prggmr\{closure}(Array, 57348)
    #1 /prggmr/src/handle.php(139): call_user_func_array(Object(Closure), Array)
    #2 /prggmr/src/engine.php(794): prggmr\Handle->execute(Array)
    #3 /prggmr/src/engine.php(766): prggmr\Engine->_execute(57348, Object(prggmr\Queue), Object(prggmr\Event), Array)
    #4 /prggmr/src/engine.php(630): prggmr\Engine->signal(57348, Array)
    #5 /prggmr/src/engine.php(747): prggmr\Engine->_search_complex(Array)
    #6 /prggmr/src/api.php(64): prggmr\Engine->signal(Array, NULL, Object(prggmr\Event))
    #7 /prggmr/tests/exceptions.php(15): signal(Array)
    #8 {main}
    thrown in /prggmr/src/engine.php on line 193

### Passing Additional Parameters

Any number of additional parameters can be provided to a signal handler.

    handle(function($name){
        echo "Hello, $name";
    }, 'hello');

    signal('name', array('Nick'));

#### Result

    Hello, Nick

Note that when passing any ```array()``` you must wrap the array.

    handle(function($array){
        echo "Result : ";
        echo $array[0] + $array[1];
    }, 'array_add', null, 2);

    // pass directly
    signal('array_add', [10, 20]);

    // pass wrapped
    signal('array_add', [[10, 20]]);

#### Result
    
    // pass directly
    Result : 0

    // pass wrapped
    Result : 30


### Passing an event

Sometimes you may need to provide your own event to your signal handlers.


    handle(function(){
        $this->cool = "Wow!";
    }, 'provide_event');

    $event = new \prggmr\Event();

    signal('provide_event', null, $event);

    echo $event->cool;

#### Result
    
    Wow