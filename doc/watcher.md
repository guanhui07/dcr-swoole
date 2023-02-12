## 热重启
现在的热重启也是监听文件变化，
然后reload。线上不会这样用，
本地开发需要的话利用inotify或fswatch做下即可，

https://github.com/buexplain/go-watch



