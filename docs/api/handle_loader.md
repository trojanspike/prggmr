# handle_loader($signal, $directory, [$heap = QUEUE_MIN_HEAP])

Registers a new handle auto-loader for the given signal.

## Return

Void

## Description

Handle loaders allow for reducing the overhead of loading your application by 
auto-loading your signal handlers just as you would auto-load classes. 

This works by recursively scanning the given directory and loading any ```.php``` 
files encountered.

Note that you can have more than one handle loader per signal but each loader
consumes 1 slot in the queue.

## Example

    handle_loader('helloworld', '/var/www/app/handles/helloworld/');

## Parameters

### $signal

A handle loader will be attached to this signal, the signal can be any string, integer or complex signal object.

### $directory

The directory to recursively scan for ```.php``` files.

### $heap
__Default :__ QUEUE_MIN_HEAP

The type of heap signal queues are using.

Note if you are using a QUEUE_MAX_HEAP and do not change this your handles will not load.

## Examples

### Complex Signal

    handle_loader(new \prggmr\signal\ArrayContains([
        'hello', 'world'
    ]), '/var/www/app/handles/helloworld');

### Creating your own handle loader

It can come to the point that creating your own handle loader will be more efficient than 
relying on the engine.

To do this simply create a handle that will always have the greatest priority.

In this example we will auto-load any user_(action) handles using a ```\prggmr\signal\Query``` signal.

    handle(function($action){
        require_once '/var/www/app/handles/user/$action.php'
    }, new \prggmr\signal\Query('user_:action'), 0, null);

#### Result

Signals such as user_add, user_edit, user_login will now automatically load their handles.