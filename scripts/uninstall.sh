#!/bin/bash

# Remove adobe script
rm -f "${MUNKIPATH}preflight.d/adobe"

# Remove adobe.plist file
rm -f "${MUNKIPATH}preflight.d/cache/adobe.plist"
