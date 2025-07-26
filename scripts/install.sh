#!/bin/bash

# adobe controller
CTL="${BASEURL}index.php?/module/adobe/"

# Get the scripts in the proper directories
"${CURL[@]}" "${CTL}get_script/adobe" -o "${MUNKIPATH}preflight.d/adobe"

# Check exit status of curl
if [ $? = 0 ]; then
	# Make executable
	chmod a+x "${MUNKIPATH}preflight.d/adobe"

	# Set preference to include this file in the preflight check
	setreportpref "adobe" "${CACHEPATH}adobe.plist"

else
	echo "Failed to download all required components!"
	rm -f "${MUNKIPATH}preflight.d/adobe"

	# Signal that we had an error
	ERR=1
fi
