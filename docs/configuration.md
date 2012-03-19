# Configuration Options
This page contains options that can be set for prggmr.

All options are set using constants defined before loading prggmr with a 
numerical or hexidecimal value.

Changing any configuration can drastically reduce performance, make sure you
know what you are doing.

## Engine

### BINARY_ENGINE_SEARCH
__Default :__ 75

Number of storage nodes required before using binary searching, changing this to 
a lower value will decrease the seek time when firing signals but will increase 
the storage time.

For low count signal systems this is recommended to leave at a higher value 
since the time required to sort the storage will outweigh the benefit of binary
searching.

## Queue

### QUEUE_MAX_SIZE
__Default :__ 24

Maximum number of handles that can be assigned to a signal.

### QUEUE_DEFAULT_PRIORITY
__Default :__ 100

Default priority for a handle entered into the queue.