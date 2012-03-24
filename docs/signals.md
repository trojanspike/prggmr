# Built-In Signals
This page contains information on the built-in prggmr signals.

All signals are represented by a hexidecimal value.

Built-in signals can be invaluable when debugging applications so they might
be worth noting.

All built-in signals are contained in the \prggmr\engine namespace defined in 
the Signals class.

Registering any signals within the range of 0xE001 - 0xE02A will result in a
RESTRICTED_SIGNAL signal.

## RESTRICTED_SIGNAL (0xE001)
A handle has been registered for a built-in signal.

    handle(function($callable, $signal, $identifier, $priority, $exhaust){
        // A restricted signal This will actually call itself :)
    }, \prggmr\engine\Signals::RESTRICTED_SIGNAL);

## INVALID_HANDLE (0xE002)
Invalid or unknown callable variable encountered.

    handle(function($handle){
        // $handle is the invalid handle
    }, \prggmr\engine\Signals::INVALID_HANDLE);

## HANDLE_EXCEPTION (0xE003)
Exception encountered during the execution of a signal handler.

    handle(function($exception, $signal){
        $handle = $exception->getHandle();
        $event = $exception->getEvent();
        $stacktrace = $exception->getTrace();
    }, \prggmr\engine\Signals::HANDLE_EXCEPTION);

## INVALID_SIGNAL (0xE004)
Invalid or unknown signal encountered.

    handle(function($signal){
        // The $signal is the invalid signal
    }, \prggmr\engine\Signals::RESTRICTED_SIGNAL);

## INVALID_EVENT (0xE005)
Invalid or unknown event encountered.

    handle(function($event){
        // The $event is the invalid event
    }, \prggmr\engine\Signals::INVALID_EVENT);

## INVALID_HANDLE_DIRECTORY (0xE005)
Invalid directory provided for handler loader.

    handle(function($event){
        // The $event is the invalid event
    }, \prggmr\engine\Signals::INVALID_HANDLE_DIRECTORY);

## SHUTDOWN (0xE015)
Engine shutdown has been initiated.

    handle(function($shutdown){
        // do some cleanup before shutdown
    }, \prggmr\engine\Signals::SHUTDOWN);

## GLOBAL_EXCEPTION (0xE016)
Global exception signal used for any non-prggmr exceptions when using the prggmr
exception handler.

    handle(function($exception){
        // the thrown exception is in $exception
    }, \prggmr\engine\Signals::GLOBAL_EXCEPTION);

## GLOBAL_ERROR (0xE017)
Global error signal used for any non-prggmr errors when using the prggmr
error handler.

    handle(function($errno, $errst, $errfile, $errline, $errcontext){
        // This is a standard PHP error handler only evented!
    }, \prggmr\engine\Signals::GLOBAL_EXCEPTION);