切换域名操作
-----------------
### 配置域名
* pic1.54php.cn（回源域名）
* cdn.pic1.54php.cn
* pic2.54php.cn（回源域名）
* cdn.pic2.54php.cn
* pic3.54php.cn（回源域名）
* cdn.pic3.54php.cn
* static.54php.cn（回源域名）
* cdn.static.54php.cn

### 七牛的相关配置都要修稿
* 每个bucket都要添加域名和修改回源地址

### 博客博文替换
* 执行脚本 php yii  blog/default/change-domain 
    * 将所有博客博文中的 相关链接 "http://www.vincentguo.cn/" 换成 "http://www.54php.cn/"
    * 将所有博客博文中的 所有图片 "http://pic2.cdn.vincentuo.cn" 换成 "http://pic2.cdn.54php.cn"

### 分发到其他平台的博文
* 全部重新推送一遍

### oauth
* 对应common/service/oauth下类的回调地址需要修改
* 所有应用的appid和appkey

## 原来vincentguo.cn下面的文章统一跳转到54php.cn下面去

