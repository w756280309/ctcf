# account：

## 表结构

 * 表名：crm_account
 *
 * @property int    $creator_id
 * @property int    $primaryContact_id  默认联系方式ID
 * @property bool   $isConverted        是否被转化(是否在温都注册)
 * @property string $type               账户类型 person
 * @property string $createTime         创建时间
 * @property string $updateTime         更新时间

## 涉及两部分：
    - 线上已注册用户的同步到account
        - 通过定时脚本命令来同步
            - 每5分钟执行一次
            - 每次执行10条
            - 时间从远到近（从离现在时间最远注册的用户开始导）
            - php yii crm/identity/import   
    - 潜在客户可通过“添加->潜客”来录入

## 客户列表
    - 显示项
        - 姓名
        - 手机
        - 固定电话
        - 年龄
        - 性别
        - 投资次数
        - 累计投资金额
        - 累计年化金额
        - 理财资产
        - 账户余额
        - 操作
    - 筛选项
        - 类型
            - 注册用户
            - 潜在客户
        - 手机
        - 固定电话