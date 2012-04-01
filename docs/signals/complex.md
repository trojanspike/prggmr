# Complex(void)

Base class for ***all*** complex signals.

If you are writing a complex signal you ***must*** inherit this class, otherwise
the engine will not know it is complex.

## Namespace

\prggmr\signal

## Abstract

Complex is an abstract class

## Description

The Complex signal is the base for all prggmr Complex signals, it provides a skeleton of both the routine and evaluate methods both of which return false by default. It also provides the ```var``` method that returns any variables assigned to the ```_vars``` property.

## Methods

### evaluate([$var = null])

Evaluates the complex signal during signal lookup when the [signal](../api/signal.html) function is called.

By default this always returns ```false``` and should be overwritten in a child signal that must evaluate in a signal lookup.

### routine([$history = null])

Runs the routing calculation during the event loop.

The return of this method will dictate how the event loop runs.

The return must always be either false or an array.

When array is returned the engine will use the array as:

node ```0``` for either another array of events to signal, a string of a single event or the  ```ENGINE_ROUTING_SIGNAL``` constant to indicate this signal should trigger.

node ```1``` for the engine idle in milliseconds. Note that the idle time returned from the routine may not be used if another signal provides a shorter time, it also is not guaranteed that the time provided will be the exact time the engine idles.

## Parent

Complex is a child of [Standard](standard.html).