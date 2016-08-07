#!/usr/bin/env bash

if [ ! -z "$SKIP_PARAM_GENERATION" ]; then
    echo "Skipping parameters.yml generation because of environment variable SKIP_PARAM_GENERATION"
    exit
fi

inventory=${SYMFONY_BUILD_ENV:-localhost}

# check if ansible exists
if hash ansible-playbook 2>/dev/null; then
    echo "Generating parameters.yml for ${inventory}"
    # run ansible playbook to generate parameters.yml
    (cd ansible && ansible-playbook -i invs/${inventory} params.yml --connection local)
else
    echo "Please install Ansible before running me"
    exit 1
fi
