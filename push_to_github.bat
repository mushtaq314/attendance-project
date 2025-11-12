@echo off
set /p repo="Enter your GitHub repo URL: "
echo Initializing Git...

git init
git add .
git commit -m "Initial commit or update"
git branch -M main

git remote remove origin 2>nul
git remote add origin %repo%

echo.
echo 🚀 Pushing code to GitHub...
git push -u origin main

echo.
echo ✅ Push complete! Check your repository online.
pause
