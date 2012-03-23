# Built-In Signals
This page contains information on the built-in prggmr signals.

All signals are represented by a hexidecimal value.

Built-in signals can be invaluable when debugging applications so they might
be worth noting.

All built-in signals are contained in the \prggmr\engine namespace defined in 
the Signals class.

Registering any signals within the range of 0xE001 - 0xE02A will result in a
RESTRICTED_SIGNAL signal.

## HANDLE_EXCEPTION (0xE001)
Exception encountered during the execution of a signal handler.

## INVALID_HANDLE (0xE002)
Invalid or unknown callable variable encountered.

## RESTRICTED_SIGNAL (0xE003)
A handle has been registered for a built-in signal.

## INVALID_SIGNAL (0xE004)
Invalid or unknown signal encountered.

## INVALID_EVENT (0xE005)
Invalid or unknown event encountered.

## SHUTDOWN (0xE015)
Engine shutdown has been initiated.

## GLOBAL_EXCEPTION (0xE016)
Global exception signal used for any non-prggmr exceptions when using the prggmr
exception handler.

## GLOBAL_ERROR (0xE017)
Global error signal used for any non-prggmr errors when using the prggmr
error handler.