#!/bin/bash
cat | kal-tokenise | divvun-cgspell -u 1.0 -n 5 /usr/share/voikko/3/kl.zhfst | /backend/spell-stream.pl
