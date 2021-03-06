define({ "api": [
  {
    "type": "post",
    "url": "api/bids",
    "title": "Place bids on auction items",
    "name": "CreateBids",
    "group": "Auction",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "amount",
            "description": "<p>bid amount</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "user_id",
            "description": "<p>id of the user submitting the bid</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "item_id",
            "description": "<p>id of the auction item which the bid is on</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Json",
            "optional": false,
            "field": "placed",
            "description": "<p>bid data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": " HTTP/1.1 201 Created\n{\n \"data\": {\n   \"amount\": 234,\n   \"user_id\": \"1\",\n   \"item_id\": 22,\n   \"updated_at\": \"2021-04-15T23:51:23.000000Z\",\n   \"created_at\": \"2021-04-15T23:51:23.000000Z\",\n   \"id\": 59\n }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "ValidationError",
            "description": "<p>validation error</p>"
          },
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "Unauthorized",
            "description": "<p>Unauthorized error</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Bad Request\n{\n  \"message\": \"Bid amount should be larger than previous bids\"\n}",
          "type": "json"
        },
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 403 Unauthorized\n{}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/BidController.php",
    "groupTitle": "Auction"
  },
  {
    "type": "get",
    "url": "api/item/:id",
    "title": "Get an auction item",
    "name": "GetAuctionItem",
    "group": "Auction",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>Id of the action item</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Json",
            "optional": false,
            "field": "matching",
            "description": "<p>auction item as data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n\"data\":\n  {\n    \"id\": 1,\n    \"name\": \"Et enim cum quia ut.\",\n    \"description\": \"Quis sed libero assumenda reiciendis distinctio maxime. Aut commodi ut error qui et ipsum. Facere modi tempore sint quisquam quisquam. Odio iure temporibus magni qui.\",\n    \"price\": 7339,\n    \"auction_end_time\": \"2021-04-15 19:04:36\",\n    \"owner_id\": null,\n    \"created_at\": \"2021-04-15T17:42:25.000000Z\",\n    \"updated_at\": \"2021-04-15T17:42:25.000000Z\"\n  }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "ValidationError",
            "description": "<p>validation error</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Bad Request\n{\n  \"error\": \"id field should be an integer\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/ItemController.php",
    "groupTitle": "Auction"
  },
  {
    "type": "get",
    "url": "api/items?filter=:filter&offset=:offset&limit=:limit&sortField=:sortField&sortOrder=:sortOrder",
    "title": "Get Auction Items",
    "name": "GetAuctionItems",
    "group": "Auction",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "filter",
            "description": "<p>string to match items by name or description</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "offset",
            "description": "<p>offset of the results</p>"
          },
          {
            "group": "Parameter",
            "type": "number",
            "optional": false,
            "field": "limit",
            "description": "<p>no of items expected to be returned</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "sortField",
            "description": "<p>sort field. should be one of name, price or auction_end_time</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "sortOrder",
            "description": "<p>sort order. should be one of ASC, asc, DESC or desc</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Json",
            "optional": false,
            "field": "matching",
            "description": "<p>auction items as data and total record count as meta</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n\"data\": [\n  {\n    \"id\": 1,\n    \"name\": \"Et enim cum quia ut.\",\n    \"description\": \"Quis sed libero assumenda reiciendis distinctio maxime. Aut commodi ut error qui et ipsum. Facere modi tempore sint quisquam quisquam. Odio iure temporibus magni qui.\",\n    \"price\": 7339,\n    \"auction_end_time\": \"2021-04-15 19:04:36\",\n    \"owner_id\": null,\n    \"created_at\": \"2021-04-15T17:42:25.000000Z\",\n    \"updated_at\": \"2021-04-15T17:42:25.000000Z\"\n  },\n  {\n    \"id\": 2,\n    \"name\": \"Delectus maiores officiis culpa omnis provident.\",\n    \"description\": \"Beatae incidunt quia nam. Laboriosam quia autem qui. Aperiam et molestias tempore non molestiae.\",\n    \"price\": 6551,\n    \"auction_end_time\": \"2021-04-21 01:04:26\",\n    \"owner_id\": null,\n    \"created_at\": \"2021-04-15T17:42:25.000000Z\",\n    \"updated_at\": \"2021-04-15T17:42:25.000000Z\"\n  },\n  {\n    \"id\": 3,\n    \"name\": \"Eaque error qui omnis dolorem sed et voluptatem.\",\n    \"description\": \"Dicta ad alias cupiditate tenetur illum. Consequatur non quos ut enim. Itaque dolor ad harum doloremque earum aut accusantium.\",\n    \"price\": 1429,\n    \"auction_end_time\": \"2021-04-25 09:04:21\",\n    \"owner_id\": null,\n    \"created_at\": \"2021-04-15T17:42:25.000000Z\",\n    \"updated_at\": \"2021-04-15T17:42:25.000000Z\"\n  },\n],\n\"meta\": {\n  \"total\": 100\n }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "ValidationError",
            "description": "<p>validation error</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Bad Request\n{\n  \"error\": \"limit field should be an integer\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/ItemController.php",
    "groupTitle": "Auction"
  },
  {
    "type": "get",
    "url": "api/autoBidStatus?itemId=:itemId&userId=:userId",
    "title": "Get auto bid status",
    "name": "GetAutoBidStatus",
    "group": "Auction",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "itemId",
            "description": "<p>Id of an action item</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "userId",
            "description": "<p>Id of a user</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Json",
            "optional": false,
            "field": "matching",
            "description": "<p>auction item as data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": " HTTP/1.1 200 OK\n{\n \"data\": {\n   \"id\": 13,\n   \"user_id\": 1,\n   \"item_id\": 22,\n   \"auto_bid_enabled\": 0,\n   \"created_at\": \"2021-04-15T23:51:20.000000Z\",\n   \"updated_at\": \"2021-04-15T23:51:20.000000Z\"\n }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "ValidationError",
            "description": "<p>validation error</p>"
          },
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "Unauthorized",
            "description": "<p>Unauthorized error</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Bad Request\n{\n  \"error\": \"id field should be an integer\"\n}",
          "type": "json"
        },
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 403 Unauthorized\n{}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/AutoBidStatusController.php",
    "groupTitle": "Auction"
  },
  {
    "type": "get",
    "url": "api/bids?itemId=:itemId",
    "title": "Get bids of an auction item",
    "name": "GetBids",
    "group": "Auction",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "itemId",
            "description": "<p>id of an auction item</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Json",
            "optional": false,
            "field": "matching",
            "description": "<p>bids</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": " HTTP/1.1 200 OK\n{\n \"data\": [\n   {\n     \"id\": 55,\n     \"amount\": 1001,\n     \"user_id\": 1,\n     \"item_id\": 2,\n     \"is_auto_bid\": 1,\n     \"created_at\": \"2021-04-15T20:51:53.000000Z\",\n     \"updated_at\": \"2021-04-15T20:51:53.000000Z\",\n     \"user\": {\n       \"id\": 1,\n       \"name\": \"user1\"\n     }\n   },\n   {\n     \"id\": 54,\n     \"amount\": 530,\n     \"user_id\": 2,\n     \"item_id\": 2,\n     \"is_auto_bid\": 1,\n     \"created_at\": \"2021-04-15T20:51:53.000000Z\",\n     \"updated_at\": \"2021-04-15T20:51:53.000000Z\",\n     \"user\": {\n       \"id\": 2,\n       \"name\": \"user2\"\n     }\n   },\n   {\n     \"id\": 51,\n     \"amount\": 529,\n     \"user_id\": 1,\n     \"item_id\": 2,\n     \"is_auto_bid\": 1,\n     \"created_at\": \"2021-04-15T20:09:29.000000Z\",\n     \"updated_at\": \"2021-04-15T20:09:29.000000Z\",\n     \"user\": {\n       \"id\": 1,\n       \"name\": \"user1\"\n     }\n   }\n ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "ValidationError",
            "description": "<p>validation error</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Bad Request\n{\n  \"error\": \"item_id is required\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/BidController.php",
    "groupTitle": "Auction"
  },
  {
    "type": "get",
    "url": "api/configurations/:userId",
    "title": "Get configurations of a user",
    "name": "GetConfiguration",
    "group": "Auction",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "userId",
            "description": "<p>Id of the user</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Json",
            "optional": false,
            "field": "matching",
            "description": "<p>configuration as data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": " HTTP/1.1 200 OK\n{\n \"data\": {\n   \"id\": 1,\n   \"user_id\": 1,\n   \"max_bid_amount\": 501,\n   \"created_at\": \"2021-04-15T17:44:30.000000Z\",\n   \"updated_at\": \"2021-04-15T22:55:03.000000Z\"\n }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "ValidationError",
            "description": "<p>validation error</p>"
          },
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "Unauthorized",
            "description": "<p>Unauthorized error</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Bad Request\n{\n  \"error\": \"id field should be an integer\"\n}",
          "type": "json"
        },
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 403 Unauthorized\n{}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/ConfigurationController.php",
    "groupTitle": "Auction"
  },
  {
    "type": "post",
    "url": "api/login",
    "title": "Login to System",
    "name": "Login",
    "group": "Auction",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "username",
            "description": "<p>user name of the user</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>password of the user</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Json",
            "optional": false,
            "field": "User",
            "description": "<p>data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": " HTTP/1.1 200 OK\n{\n   \"data\": {\n     \"id\":\"1\",\n     \"name\":\"user1\",\n     \"api_token\":\"LQ7fI13n3GIazTslIH0Z4R2tT78QmJbX8Nd1J8355ZMYoSHZxGvIkiSY4ds0\",\n     \"created_at\":\"2021-04-15T23:09:59.000000Z\",\n     \"updated_at\":\"2021-04-15T23:09:59.000000Z\",\n   }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "Unauthorized",
            "description": "<p>Login Credentials Mismatch</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 403 Unauthorized\n{\n  \"message\": \"Login Credentials Mismatch\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/AuthController.php",
    "groupTitle": "Auction"
  },
  {
    "type": "patch",
    "url": "api/autoBidStatus?itemId=:itemId&userId=:userId",
    "title": "Update auto bid status",
    "name": "UpdateAutoBidStatus",
    "group": "Auction",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "itemId",
            "description": "<p>Id of an action item</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "userId",
            "description": "<p>Id of a user</p>"
          },
          {
            "group": "Parameter",
            "type": "Boolean",
            "optional": false,
            "field": "auto_bid_enabled",
            "description": "<p>Auto bid enabled or not</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Json",
            "optional": false,
            "field": "matching",
            "description": "<p>auction item as data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": " HTTP/1.1 200 OK\n{\n \"data\": {\n   \"id\": 13,\n   \"user_id\": 1,\n   \"item_id\": 22,\n   \"auto_bid_enabled\": true,\n   \"created_at\": \"2021-04-15T23:51:20.000000Z\",\n   \"updated_at\": \"2021-04-16T00:16:54.000000Z\"\n }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "ValidationError",
            "description": "<p>validation error</p>"
          },
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "Unauthorized",
            "description": "<p>Unauthorized error</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Bad Request\n{\n  \"error\": \"user_id field should be an integer\"\n}",
          "type": "json"
        },
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 403 Unauthorized\n{}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/AutoBidStatusController.php",
    "groupTitle": "Auction"
  },
  {
    "type": "patch",
    "url": "api/configuration/:userId",
    "title": "Update user configuration",
    "name": "UpdateConfiguration",
    "group": "Auction",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "userId",
            "description": "<p>Id of the user</p>"
          },
          {
            "group": "Parameter",
            "type": "Boolean",
            "optional": false,
            "field": "max_bid_amount",
            "description": "<p>Maximum auto bid amount</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Json",
            "optional": false,
            "field": "matching",
            "description": "<p>configuration as data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": " HTTP/1.1 200 OK\n{\n \"data\": {\n   \"id\": 1,\n   \"user_id\": 1,\n   \"max_bid_amount\": 501,\n   \"created_at\": \"2021-04-15T17:44:30.000000Z\",\n   \"updated_at\": \"2021-04-15T22:55:03.000000Z\"\n }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "fields": {
        "Error 4xx": [
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "ValidationError",
            "description": "<p>validation error</p>"
          },
          {
            "group": "Error 4xx",
            "optional": false,
            "field": "Unauthorized",
            "description": "<p>Unauthorized error</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 400 Bad Request\n{\n  \"error\": \"max_bid_amount field should be a number\"\n}",
          "type": "json"
        },
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 403 Unauthorized\n{}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "app/Http/Controllers/ConfigurationController.php",
    "groupTitle": "Auction"
  }
] });
