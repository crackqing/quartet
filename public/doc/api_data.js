define({ "api": [
  {
    "type": "POST",
    "url": "/api/v1/bindInviteCode",
    "title": "绑定邀请码",
    "group": "________Agent",
    "version": "1.0.0",
    "description": "<p>绑定邀请码</p>",
    "parameter": {
      "fields": {
        "参数": [
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "bind_id",
            "description": "<p>绑定ID (80001) 五位ID</p>"
          },
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "mobile",
            "description": "<p>手机号码</p>"
          },
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "agent_id",
            "description": "<p>玩家UID</p>"
          },
          {
            "group": "参数",
            "type": "String",
            "optional": false,
            "field": "agent_nickname",
            "description": "<p>玩家名称</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n    \"bind_id\" : \"600\",\n    \"mobile\" : \"3000xx\",\n    \"agent_id\" : \"nick\",\n    \"agent_nickname\" : \"nick\",\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"status\": \"success\",\n    \"code\": 200,\n    \"message\": \"绑定成功\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/Api/ExteriorController.php",
    "groupTitle": "________Agent",
    "name": "PostApiV1Bindinvitecode"
  },
  {
    "type": "POST",
    "url": "/api/v1/checkBindExist",
    "title": "检测邀请码是否存在",
    "group": "________Agent",
    "version": "1.0.0",
    "description": "<p>检测邀请码是否存在</p>",
    "parameter": {
      "fields": {
        "参数": [
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "agent_id",
            "description": "<p>代理ID</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n    \"agent_id\" : \"80000\",\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"status\": \"success\",\n    \"code\": 200,  |  2001 不存在的绑定ID\n    \"message\": \"已绑定\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/Api/ExteriorController.php",
    "groupTitle": "________Agent",
    "name": "PostApiV1Checkbindexist"
  },
  {
    "type": "POST",
    "url": "/api/v1/checkBindStatus",
    "title": "检测uid绑定状态",
    "group": "________Agent",
    "version": "1.0.0",
    "description": "<p>检测uid绑定状态</p>",
    "parameter": {
      "fields": {
        "参数": [
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "bind_id",
            "description": "<p>玩家UID</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n    \"bind_id\" : \"300001\",\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"status\": \"success\",\n    \"code\": 200,  |  2001 不存在的绑定ID\n    \"message\": \"已绑定\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/Api/ExteriorController.php",
    "groupTitle": "________Agent",
    "name": "PostApiV1Checkbindstatus"
  },
  {
    "type": "POST",
    "url": "/api/v1/inviteSw",
    "title": "二维码(开关)",
    "group": "________Agent",
    "version": "1.0.0",
    "description": "<p>二维码绑定功能开关状态判断</p>",
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"status\": success,\n    \"code\": '200',\n    \"data\" : ['switch' => 1 or 0]\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/Api/ExteriorController.php",
    "groupTitle": "________Agent",
    "name": "PostApiV1Invitesw"
  },
  {
    "type": "POST",
    "url": "/api/v1/gameHallKindid",
    "title": "获取游戏桌子数据",
    "group": "________HallKindid",
    "version": "1.0.0",
    "description": "<p>获取对应的游戏桌子数据（后台编辑）</p>",
    "parameter": {
      "fields": {
        "参数": [
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "kindid",
            "description": "<p>游戏ID</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"status\": success,\n    \"code\": '200',\n    \"data\" : ['captcha' => '356789']\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/Api/ExteriorController.php",
    "groupTitle": "________HallKindid",
    "name": "PostApiV1Gamehallkindid"
  },
  {
    "type": "POST",
    "url": "/api/v1/cashSync",
    "title": "兑换Sync",
    "group": "________Syncs",
    "version": "1.0.0",
    "description": "<p>兑换Sync</p>",
    "parameter": {
      "fields": {
        "参数": [
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "cash_money",
            "description": "<p>兑换金钱 (以分为单位.)</p>"
          },
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "agent_id",
            "description": "<p>代理ID,相当于玩家UID</p>"
          },
          {
            "group": "参数",
            "type": "String",
            "optional": false,
            "field": "agent_nickname",
            "description": "<p>玩家名称</p>"
          },
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "coins",
            "description": "<p>玩家当前的余额金币</p>"
          },
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "bank",
            "description": "<p>玩家当前的银行数钱</p>"
          },
          {
            "group": "参数",
            "type": "String",
            "optional": false,
            "field": "exchangeType",
            "description": "<p>兑换方式 1:支付宝,2:银行,3:微信</p>"
          },
          {
            "group": "参数",
            "type": "String",
            "optional": false,
            "field": "realname",
            "description": "<p>玩家名字</p>"
          },
          {
            "group": "参数",
            "type": "String",
            "optional": false,
            "field": "account",
            "description": "<p>兑换到的账号</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n    \"cash_money\" : \"600\",\n    \"agent_id\" : \"3000xx\",\n    \"agent_nickname\" : \"nick\",\n    \"coins\" : \"10000\",\n    \"bank\" : \"0\",\n    \"exchangeType\" : \"zfb\",\n    \"realname\" : \"realname\",\n    \"account\" : \"account\",\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"status\": \"success\",\n    \"code\": 200,\n    \"message\": \"订单同步成功\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/Api/ExteriorController.php",
    "groupTitle": "________Syncs",
    "name": "PostApiV1Cashsync"
  },
  {
    "type": "POST",
    "url": "/api/v1/orderSync",
    "title": "订单Sync",
    "group": "________Syncs",
    "version": "1.0.0",
    "description": "<p>订单Sync</p>",
    "parameter": {
      "fields": {
        "参数": [
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "price",
            "description": "<p>充值金额,单位为分</p>"
          },
          {
            "group": "参数",
            "type": "String",
            "optional": false,
            "field": "order_id",
            "description": "<p>充值的商户订单号</p>"
          },
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "agent_id",
            "description": "<p>代理ID,相当于玩家UID</p>"
          },
          {
            "group": "参数",
            "type": "String",
            "optional": false,
            "field": "pay_type",
            "description": "<p>zfb,wx,unipony,qq ==</p>"
          },
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "coins",
            "description": "<p>玩家当前的余额金币</p>"
          },
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "bank",
            "description": "<p>玩家当前的银行数钱</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n    \"price\" : \"600\",\n    \"order_id\" : \"order-xxxxx1111\",\n    \"agent_id\" : \"3000xx\",\n    \"pay_type\" : \"zfb\",\n    \"coins\" : \"10000\",\n    \"bank\" : \"0\",    *\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"status\": \"success\",\n    \"code\": 200,   | 1001 为订单重复\n    \"message\": \"订单同步成功\"\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/Api/ExteriorController.php",
    "groupTitle": "________Syncs",
    "name": "PostApiV1Ordersync"
  },
  {
    "type": "POST",
    "url": "/api/v1/cashSeting",
    "title": "兑换配置",
    "group": "________pay",
    "version": "1.0.0",
    "description": "<p>兑换配置</p>",
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"status\": success,\n    \"code\": '200',\n    \"data\" : []\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/Api/ExteriorController.php",
    "groupTitle": "________pay",
    "name": "PostApiV1Cashseting"
  },
  {
    "type": "POST",
    "url": "/api/v1/paySeting",
    "title": "支付配置",
    "group": "________pay",
    "version": "1.0.0",
    "description": "<p>支付配置</p>",
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"status\": success,\n    \"code\": '200',\n    \"data\" : []\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/Api/ExteriorController.php",
    "groupTitle": "________pay",
    "name": "PostApiV1Payseting"
  },
  {
    "type": "POST",
    "url": "/api/v1/qrcodeImages",
    "title": "二维码",
    "group": "________pay",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "参数": [
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "user_id",
            "description": "<p>用户ID 查找绑定上级返回的二维码生成</p>"
          }
        ]
      }
    },
    "description": "<p>二维码--直接访问 https://ssl.dfylpro.com/ 苹果安装</p>",
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"status\": success,\n    \"code\": '200',\n    \"data\" : [\"path\":'http://xxxxxxx/xxx.png']\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/Api/ExteriorController.php",
    "groupTitle": "________pay",
    "name": "PostApiV1Qrcodeimages"
  },
  {
    "type": "POST",
    "url": "/api/v1/vipSeting",
    "title": "VIP配置",
    "group": "________pay",
    "version": "1.0.0",
    "description": "<p>VIP配置</p>",
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"status\": success,\n    \"code\": '200',\n    \"data\" : []\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/Api/ExteriorController.php",
    "groupTitle": "________pay",
    "name": "PostApiV1Vipseting"
  },
  {
    "type": "POST",
    "url": "状态码说明,参考对应接口的错误码",
    "title": "",
    "group": "_______errorCodeStats",
    "version": "1.0.0",
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 Response Code\n{\n \"status\": \"success\", |failed  错误只会返回 success 与 failed\n \"code\": 200,\n \"message\": \"已绑定\"\n\n    200  ---- {{ 只要为200都是成功的操作 }}\n    400  ---- {{  400 http error  }}\n    403  ---- {{  403 no Permission  }}\n    404  ---- {{  404 no found   }}\n    500  ---- {{  500 服务器错误 }}\n    1001 ---- {{ 为订单重复 }}\n    2001 ---- {{ 不存在的绑定ID }}\n    2002 ---- {{ 重复添加 }}\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/Api/ExteriorController.php",
    "groupTitle": "_______errorCodeStats",
    "name": "Post"
  },
  {
    "type": "POST",
    "url": "/api/v1/activetySmsCaptcha",
    "title": "短信验证码",
    "group": "_______thirdCall",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "参数": [
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "phone",
            "description": "<p>手机号码1开头3-9 号段支付,13位。</p>"
          },
          {
            "group": "参数",
            "type": "String",
            "optional": false,
            "field": "thrid",
            "description": "<p>默认为common,不传也可用.  新增tiantian渠道短信</p>"
          }
        ]
      }
    },
    "description": "<p>短信 10分内单个手机可发送3次，防止恶意调用.^_^</p>",
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"status\": success,\n    \"code\": '200',\n    \"data\" : ['captcha' => 'XXXX']\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/Api/ExteriorController.php",
    "groupTitle": "_______thirdCall",
    "name": "PostApiV1Activetysmscaptcha"
  },
  {
    "type": "POST",
    "url": "/api/v1/statistics",
    "title": "统计(日活,新增,在线)",
    "group": "______statis",
    "version": "1.0.0",
    "description": "<p>登录统计 (直接发送,不用管返回处理.)</p>",
    "parameter": {
      "fields": {
        "参数": [
          {
            "group": "参数",
            "type": "Number",
            "optional": false,
            "field": "uid",
            "description": "<p>用户ID</p>"
          },
          {
            "group": "参数",
            "type": "string",
            "optional": false,
            "field": "enum",
            "description": "<p>类型(登录为login,新增为new,实时在线为online)</p>"
          },
          {
            "group": "参数",
            "type": "string",
            "optional": false,
            "field": "agent_nickname",
            "description": "<p>玩家名称</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Request-Example:",
          "content": "{\n    \"uid\" : \"1111xx\"\n    \"enum\" : \"1111xx\",\n    \"agent_nickname\" : \"string....\",\n\n}",
          "type": "json"
        }
      ]
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "{\n    \"status\": success,\n    \"code\": '200',\n    \"message\" : 'sucess'\n}",
          "type": "json"
        }
      ]
    },
    "filename": "app/Http/Controllers/Api/ExteriorController.php",
    "groupTitle": "______statis",
    "name": "PostApiV1Statistics"
  }
] });
