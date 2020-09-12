# 修改gitee提交push时的账号密码错误

- 测试在wim10通过窗口操作有点麻烦，以下是通过命令行方式删除掉
- gitee的在git里的登录账号和密码失效后的处理方法

## 通过CMD命令行取消已经保存的账号密码凭证
- 在 cmd 里面输入: cmdkey 参数的使用
- cmdkey.exe，是凭据管理器命令行实用工具。



 
## 操作步骤：

1. 要列出可用的凭据:
   - cmdkey /list
   - 如果有，在这里会看到一条关于gitee的登录凭据
2. 要删除现有凭据: 
   cmdkey /delete:targetname
通过这个命令删除掉，这里的targetname很长，通过右键复制一下
3. 重新登录：
   再次运行git push 会弹出一个再次登录的窗口。输入正确的gitee登录账号密码即可。





 

