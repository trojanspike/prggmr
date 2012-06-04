# TODO

## Event TTL (Time to live)
Setup to allow for signals to register their provided events to have a TTL
of a set amount of time in milliseconds.

If the _execute method encounters an event that has passed its TTL it will
signal a prggmr\engine\Signals::EVENT_EXPIRED signal.