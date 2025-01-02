#!/bin/bash
cd /var/www/html/nextjs
npm run build
pm2 restart next

