#!/bin/bash
# Install postgresql client
sudo apt-get update && sudo apt-get install -y postgresql-client
# We can't install a full postgresql server inside this docker container easily, so we will use sqlite for tests but mock the RLS statements
