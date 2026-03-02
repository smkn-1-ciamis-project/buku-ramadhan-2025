#!/usr/bin/env pwsh

# Force abort any in-progress merge
Remove-Item .git\MERGE_* -Force -ErrorAction SilentlyContinue
Remove-Item .git\ORIG_HEAD -Force -ErrorAction SilentlyContinue  
Remove-Item .git\.MERGE_MSG.swp -Force -ErrorAction SilentlyContinue
Remove-Item .git\COMMIT_EDITMSG -Force -ErrorAction SilentlyContinue
Remove-Item .git\AUTO_MERGE -Force -ErrorAction SilentlyContinue

Write-Host "✓ Merge state cleared"
git status
