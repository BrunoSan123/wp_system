@echo off
title create commit log
git log --pretty=format:%h,%an,%ae,%s > log.csv
pause
