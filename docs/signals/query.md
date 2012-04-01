# Query

Signals event based on a query string lookthrough.

## Namespace

\prggmr\signal

## Description

This attempts to provide a simple method to signal events based on a query of a string.

Syntax for parameters is ```:param_name```

It uses a regular expression for the parameter matching.

The regular expression syntax.

```/user/:name```

```#user/(?P<name>[\w]+)$#i```

## Example

    handle(function($name){
        echo "Hello $name";
    }, new \prggmr\signal\Query('user/:name'));

## Methods

### __construct($query)

Constructs a new Query signal.

#### $query

The querystring to evaluate.

### evaluate($signal)

Evalutes the given signal to determine if it should trigger the signal.

Returning any found query parameters to pass to signal handlers.

#### $signal

Signal to evaluate

#### Returns

Boolean

Returns boolean ```false``` when evaluation fails or ```true``` when evaluation is true and no variables are found.

Array

The array of variables found during the query lookthough.

## Parent

Query is a child of [Complex](complex.html).