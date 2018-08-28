#!/bin/bash

if [ $TRAVIS_BRANCH == 'master' ] ; then
    scp -r ./_site/* $scp_dest
fi