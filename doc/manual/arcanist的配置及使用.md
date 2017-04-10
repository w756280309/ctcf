arcanist(代码审核工具)

# 本地配置

- 新建一个文件夹, 克隆 libphutil 和 arcanist 项目:

```
mkdir ~/phabricator #文件夹可以自定义，建议放到 /home/当前登录用户/^
cd ~/phabricator
which git #执行此命令,查看有没有安装git
sudo apt-get install git #如果没有安装git，那么就执行此命令安装git
git clone https://github.com/phacility/libphutil.git
git clone https://github.com/phacility/arcanist.git

测试如何是否安装成功:

cd ~/phabricator/arcanist/bin
arc #如果显示Try `arc help`字样代表已经安装成功

配置全局环境变量:

export PATH="$PATH:/home/vagrant/phabricator/arcanist/bin" #目录一定要根据自己的目录进行调整,并且确保命令的正确性
echo $PATH #查看指定的目录是否已被加入环境变量

修改默认编辑器:

# arc本身的编辑器比较难使用,所以通常会换成比较容易使用的编辑器
which vim
arc set-config editor + vim路径  #vim路径指的是上面which vim输出的路径
```

# 使用

- 首先, 进入要使用 arc 命令的项目, 查看是否存在配置 .arcconfig 的配置文件, 如下:
```
.arcconfig

{
    "phabricator.uri": "http://dx.wangcaigu.cn/"
}
```

如果没有该配置文件,就无法使用arc,里面的地址配置的是arc diff所要上传的远端服务器地址,新生成的diff文件,都要上传到指定的远端服务器上

- 首次在项目中使用arc,会提示用户执行如下命令:
```
arc install-certificate
```
按照提示完成Api Token的添加

- 开发步骤:

 1. 使用git新建分支, 如下:
```
git fetch origin master/prod  #拉取最新的master或prod分支
git checkout -b f3444 origin/master #基于远端master分支,新建f3444分支,这里的f3444和master都为分支名称,可以替换为其他的分支名称
```
 2. 开发代码;
 3. commit 代码,如下:
```
git add .
git commit -m 提交描述信息
```
 4. 使用 arc 命令,提交diff,注意这里,不用再 push 分支了,直接发diff文件就好,如下:
```
arc diff #提交一个新的diff,通常等同于arc diff --create
arc diff --create #新建一个diff,如果该分支已经发过diff,但是又想新建一个diff的时候,可以使用此命令
arc diff --update D123 #更新一个diff, D123为diff的版本号,此命令适用于修改一个已经存在的diff文件
arc diff origin/master #此命令试用于从远端拉取一个已经存在的分支,然后直接发diff的情况, master是当前分支的父分支
arc patch D123 #此命令可以将远端的diff patch到本地,所做的操作,就是将远端的diff拉取到本地,并新建一个分支,我们可以基于这个分支做一些操作
```
 5. diff 命令执行后,进入编辑diff页面,这个时候,我们需要输入如下信息,保存退出,然后会自动提交,如下:
 ```
#这里填写diff的标题

Summary: #填写 diff 描述

Test Plan: #填写测试方案,这块通常是给测试人员看的

Reviewers: #填写走查人

Subscribers:  #填写订阅者,通常为空
 ```
 6. diff 发成功后,会生成一个访问链接,我们可以通过点击这个链接,查看diff的具体信息。
 
 NOTE:
 1. 如果想要update其他人的diff, 这时直接arc diff会报错, 需要在远端服务器打开对应的diff, 在下方选项中选择Commandeer Revision后, 方可进行update操作;