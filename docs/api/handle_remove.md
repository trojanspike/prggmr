# handle_remove($handle, $signal)

Removes a registered signal handle.

## Return

Void

## Example

    $handle = handle(function(){}, 'my_signal');
    handle_remove($handle, 'my_signal');

## Parameters

### $handle

The ```\prggmr\Handle``` instance returned by ```handle```

### $signal

The signal the handle is assigned to.