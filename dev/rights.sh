#!/bin/bash
find .. -type f -exec chmod 644 {} \;
find .. -type d -exec chmod 755 {} \;
find .. -name "*.sh"  -exec chown root: {} \;
find .. -name "*.sh"  -exec chmod u+x {} \;
