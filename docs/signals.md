# Built-In Signals
This page contains information on the built-in prggmr signals.

All signals are represented by a hexidecimal value.

Built-in signals can be invaluable when debugging applications so they might
be worth noting.

All built-in signals are contained in the \prggmr\engine namespace defined in 
the Signals class.

Registering any signals within the range of 0xE001 - 0xE02A will result in a
RESTRICTED_SIGNAL signal being trigged.

## HANDLE_EXCEPTION (0xE001)
Exception encountered during the execution of a sig handler.

## INVALID_HANDLE (0xE002)
Invalid or unknown callable function given to register as a sig handler.

## SHUTDOWN (0xE015)
Engine shutdown is starting.

## GLOBAL_EXCEPTION (0xE016)
Global exception signal used for any non-prggmr exceptions when using the prggmr
exception and error handler.

## GLOBAL_ERROR (0xE017)
Global error signal used for any non-prggmr errors when using the prggmr
exception and error handler.