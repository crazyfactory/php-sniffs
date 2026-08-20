#!/bin/bash

if [[ "$GITHUB_REF_TYPE" != "tag" ]]; then
    echo "Not a tag build, skipping deploy"
    exit 0
fi

if [ -z "$GEMFURY_USER" ] || [ -z "$GEMFURY_PASSWORD" ] || [ -z "$GEMFURY_REPO_SLUG" ]; then
    echo "Missing Gemfury credentials, cannot deploy"
    exit 1
fi

echo "Deploying tag ${GITHUB_REF_NAME}..."

printf "machine git.fury.io\nlogin $GEMFURY_USER\npassword $GEMFURY_PASSWORD" > ~/.netrc
chmod 600 ~/.netrc

git remote add fury "https://git.fury.io/$GEMFURY_REPO_SLUG.git"
git fetch fury
git push fury "refs/tags/${GITHUB_REF_NAME}"
sleep 5
git push fury HEAD:master
