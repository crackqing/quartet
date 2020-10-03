## 运营后台API
    2018项目->独立开发由产品提出需求,分析需求使用MySQLWorkbench分析对应关系. 先写基础API接口. 在写对应的业务接口.配合前端组完成
[演示地址]
- [x] 引入l5-repository完成LoginController分层C增加Service Model增加R层. 
- [ ] 权限角色与菜单侍完成分层 ,后续做成通用的API接口.业务放入package包.
- [x] 安装数据库,部分是在migrations完成.统一转化在migrations.方便他人引入安装,并增加填充数据.
- [ ] 优化引入Laravel-swoole. 
- [ ] 部分覆盖Test

#### 文档工具
**postman** quartet.postman_collection.json WEB前端组对接

**apidoc** 生成文档直接写在控制器当中(ExteriorController apidoc -i -o)游戏后端组对接文档  安装apidoc(npm -g install apidoc)


#### 部署 开发兼运维(centos LNMP)

- **nginx** 负载均衡（LVS) 防止单点故障,优化应用访问.
- **deploy** 自动化部署,一键同步到集群服务器.
- **sentry** 监控程序异常服务,及时修复与处理线上异常.
- **gitlab** docker一键搭建内部的GIT管理平台.

#### 技术简介
- **PASSPORT**(Laravel内置的登录认证服务oauth2.0).
- **ZIZACO/ENTRUST** 第三方库,实现基于角色的RBAC认证接口,并加入自定义中间件完成权限验证.
- **QUEUE REDIS**框架内置的队列服务用于邮件通知,手机短信,加速应用访问返回.
- **CRONTAB**任务调度,处理每天的订单,充值,兑换与游戏相关的数据统计. 发送邮件报表,备份等操作.

#### 项目图片
![用户管理—用户](http://pp29dvc6r.bkt.clouddn.com/quartet_user.png)
![用户管理—系统](http://pp29dvc6r.bkt.clouddn.com/quartet_rbac.png)
![postman文档说明](http://pp29dvc6r.bkt.clouddn.com/postman.png)


