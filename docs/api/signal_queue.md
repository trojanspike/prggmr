# signal_queue($signal, $create = true, $type = QUEUE_MIN_HEAP)

Locates or creates a signal queue in the engine.

## Return

__Boolean :__ False

False is returned when ```$create``` is false and the queue could not be located.

__Array__

An array is returned when ```$create``` is true.

Node ```0``` contains the flag for the queue creation, this allows to determine if
the queue is new, empty or contains handles.

Node ```1``` contains the ```\prggmr\Queue``` instance representing the signal.

    [
        \prggmr\Engine::QUEUE_NEW|\prggmr\Engine::QUEUE_EMPTY|\prggmr\Engine::QUEUE_NONEMPTY,
        \prggmr\Queue
    ]

## Example

    interval(function(){
        $queue = signal_queue('helloworld');
        switch($queue[0]) {
            case \prggmr\Engine::QUEUE_NEW:
                echo "A new queue for the helloworld signal has been created";
                break;
            case \prggmr\Engine::QUEUE_EMPTY:
                echo "Located the helloworld signal queue but it is empty.";
                handle(function(){
                    echo "Helloworld";
                }, "helloworld", null, null);
                break;
            case \prggmr\Engine::QUEUE_NONEMPTY:
                echo "Located the helloworld signal queue and it is not empty.";
                echo "Signaling helloworld";
                signal("helloworld");
                break;
        }
    }, 1000);

#### Results

    A new queue for the helloworld signal has been created
    Located the helloworld signal queue but it is empty.
    Located the helloworld signal queue and it is not empty.
    Signaling helloworld
    Helloworld
    Located the helloworld signal queue and it is not empty.
    Signaling helloworld
    Helloworld
    Located the helloworld signal queue and it is not empty.
    Signaling helloworld
    Helloworld
    ...

Read more about <a href="../event_history.html">the event history</a>.