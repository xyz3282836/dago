<?php

return [

    //每页多少条目
    'perpage'           => 10,

    /**
     * form
     */
    'time_typec'        => [
        1 => '限时下单(24小时)',
        3 => '普通下单(36小时)'
    ],
    'cfr_typec'         => [
        1 => '全部状态',
        2 => '待提交文字',
        3 => '留评中',
        4 => '留评完成',
    ],
    'cfr_sitec'         => [
        0  => '全部站点',
        1  => '美国',
        2  => '加拿大',
        3  => '英国',
        4  => '德国',
        5  => '法国',
        6  => '日本',
        8  => '西班牙',
        10 => '意大利',
    ],
    'promotion_statusc' => [
        'all' => '全部状态',
        1     => '进行中',
        3     => '已完成',
    ],
    'wishlist_statusc'  => [
        'all' => '全部状态',
        1     => '进行中',
        3     => '已完成',
    ],
    'qa_statusc'        => [
        'all' => '全部状态',
        1     => '进行中',
        3     => '已完成',
    ],
    'order_ptypec'      => [
        1 => '支付宝',
    ],
    'from_sitec'        => [
        1  => '美国',
        2  => '加拿大',
        3  => '英国',
        4  => '德国',
        5  => '法国',
        6  => '日本',
        8  => '西班牙',
        10 => '意大利',
    ],
    /**
     * form
     */

    /**
     * db-text
     */
    'delivery_type'     => [
        1 => '自行转运',
        2 => '达购转运'
    ],
    'order_ptype'       => [
        0 => '金币',
        1 => '支付宝',
    ],
    'time_type'         => [
        1 => '限时下单',
        3 => '普通下单'
    ],
    'cf_status'         => [
        0 => '取消订单',
        1 => '待支付',
        2 => '已经支付',
        3 => '正在找寻代购账号',
        4 => '购买中',
        5 => '购买完成',
        6 => '进行中',
    ],
    'from_site'         => [
        1  => '美国',
        2  => '加拿大',
        3  => '英国',
        4  => '德国',
        5  => '法国',
        6  => '日本',
        7  => '',
        8  => '西班牙',
        9  => '',
        10 => '意大利',
    ],
    'currency'          => [
        1 => '美元',
        2 => '加拿大元',
        3 => '英镑',
        4 => '欧元',
        5 => '日元',
    ],
    'bill_type'         => [
        0  => '注册送金币',
        1  => '充值金币',
        2  => '代购消费',
        3  => '退款',
        4  => '评价',
        5  => '上传图片',
        6  => '上传视频',
        7  => '订单异常补偿',
        8  => '点赞/点踩',
        9  => '心愿单',
        10 => 'Q&A',
    ],
    'order_status'      => [
        0 => '已删除',
        1 => '待付款',
        2 => '已付款',
        3 => '进行中',
        4 => '全部完成',
        5 => '全部失败',
        6 => '部分失败',
        8 => '冻结',
    ],
    'cfresult_status'   => [
        -1 => '已退款',
        0  => '购买失败',
        1  => '代购中',
        2  => '已发货',
        3  => '成功送达',
        4  => '代购失败',
    ],
    'cfresult_estatus'  => [
        1 => '未提交',
        2 => '已提交',
        3 => '同步',
        4 => '锁定',
        5 => '评价成功',
        6 => '评价失败',
        7 => '重复',
    ],
    'order_type'        => [
        1  => '充值',
        2  => '消费',
        3  => '退款',
        4  => '评价',
        5  => '上传图片',
        6  => '上传视频',
        7  => '订单异常补偿',
        8  => '点赞/点踩',
        9  => '心愿单',
        10 => 'Q&A',
    ],
    'user_level'        => [
        1 => '普通会员',
        2 => '认证会员'
    ],
    'banner_type_text'  => [
        1 => '轮播banner',
        2 => 'logo',
        3 => '购物车页banner',
        4 => '新建页面banner',
        5 => '首页图片',
        6 => '首页logo',
    ],
    'user_evaluate'     => [
        0 => '禁止评价',
        1 => '可评价'
    ],
    'is_fba'            => [
        0 => '非亚马逊发货(FBM·不可评价)',
        1 => '亚马逊发货(FBA)'
    ],
    'promotion_status'  => [
        -1 => '已退款',
        0  => '失败',
        1  => '进行中',
        2  => '同步',
        3  => '已完成',
    ],
    'wishlist_status'   => [
        -1 => '已退款',
        0  => '失败',
        1  => '进行中',
        2  => '同步',
        3  => '已完成',
    ],
    'qa_status'         => [
        -1 => '已退款',
        0  => '失败',
        1  => '进行中',
        2  => '同步',
        3  => '已完成',
    ],
    'promotion_type'    => [
        1 => '点赞',
        2 => '点踩',
    ],
    /**
     * db-text
     */

    /**
     * 自定义
     */
    'cfresult_statuss'  => [
        -1 => '已退款',
        0  => '购买失败',
        1  => '代购中',
        2  => '已发货',
        3  => '成功送达',
        4  => '代购完成，进入亚马逊pending阶段（通常需要4-5天才开始发货请耐心等待）',
        6  => '代购中',
    ],
    'cfresult_estatuss' => [
        1 => '评价',
        2 => '修改评价',
        3 => '修改评价',
        4 => '留评中',
        7 => '修改评价',
        5 => '已留评',
        6 => '留评失败',
    ],
    'bill_types'        => [
        -1 => '综合',
        1  => '充值金币',
        2  => '代购消费',
        3  => '退款',
        4  => '评价',
        5  => '上传图片',
        6  => '上传视频',
        7  => '订单异常补偿',
        8  => '点赞/点踩',
        9  => '心愿单',
        10 => 'Q&A',

    ],
    'order_statuss'     => [
        0 => '全部',
        1 => '待付款',
        2 => '已付款',
    ],
    'admin_order_type'  => [
        'all' => '全部',
        1     => '充值',
        2     => '消费',
        3     => '退款',
        4     => '评价',
        5     => '上传图片',
        6     => '上传视频',
        7     => '订单异常补偿',
        8     => '点赞/点踩',
        9     => '心愿单',
        10    => 'Q&A',
    ],
    'cf_search_type'    => [
        1 => '关键词搜索',
        2 => 'CPC下单',
        3 => '添加WL下单',
        0 => '今日代购',
    ],
    'cf_fba'            => [
        0 => 'FBM',
        1 => 'FBA',
    ],
    'promotion_statuss' => [
        -1 => '已退款',
        0  => '失败',
        1  => '进行中',
        2  => '进行中',
        3  => '已完成',
    ],
    'wishlist_statuss'  => [
        -1 => '已退款',
        0  => '失败',
        1  => '进行中',
        2  => '进行中',
        3  => '已完成',
    ],
    'qa_statuss'        => [
        -1 => '已退款',
        0  => '失败',
        1  => '已提交，待提问',
        2  => '已提交，待提问',
        3  => '已完成提问',
    ],
    /**
     * 自定义
     */

    'bigc' => [
        0  => '不选类别直接搜索',
        1  => 'Alexa Skills',
        2  => 'Amazon Video',
        3  => 'Amazon Warehouse Deals',
        4  => 'Appliances',
        5  => 'Apps & Games',
        6  => 'Arts, Crafts & Sewing',
        7  => 'Automotive Parts & Accessories',
        8  => 'Baby',
        9  => 'Beauty & Personal Care',
        10 => 'Books',
        11 => 'CDs & Vinyl',
        12 => 'Cell Phones & Accessories',
        13 => 'Clothing, Shoes & Jewelry',
        14 => 'Women',
        15 => 'Men',
        16 => 'Girls',
        17 => 'Boys',
        18 => 'Baby',
        19 => 'Collectibles & Fine Art',
        20 => 'Computers',
        21 => 'Courses',
        22 => 'Credit and Payment Cards',
        23 => 'Digital Music',
        24 => 'Electronics',
        25 => 'Gift Cards',
        26 => 'Grocery & Gourmet Food',
        27 => 'Handmade',
        28 => 'Health, Household & Baby Care',
        29 => 'Home & Business Services',
        30 => 'Home & Kitchen',
        31 => 'Industrial & Scientific',
        32 => 'Kindle Store',
        33 => 'Luggage & Travel Gear',
        34 => 'Luxury Beauty',
        35 => 'Magazine Subscriptions',
        36 => 'Movies & TV',
        37 => 'Musical Instruments',
        38 => 'Office Products',
        39 => 'Patio, Lawn & Garden',
        40 => 'Pet Supplies',
        41 => 'Prime Pantry',
        42 => 'Software',
        43 => 'Sports & Outdoors',
        44 => 'Tools & Home Improvement',
        45 => 'Toys & Games',
        46 => 'Vehicles',
        47 => 'Video Games',
        48 => 'Wine',
    ],

    'sortc' => [
        1 => 'Price: Low to High',
        2 => 'Price: High to Low',
        3 => 'Avg. Customer Review',
        4 => 'Newest Arrivals',
    ],

    'placec' => [
        1 => 'Frequently Bought Together',
        2 => 'Sponsored Products Related To This Item',
        3 => 'Customers Who Bought This Item Also Bought',
        4 => 'Customers Also Shopped For',
        5 => 'What Other Items Do Customers Buy After Viewing This Item?',
        6 => 'Compare to Similar Items',
        7 => 'customers who viewd this item also viewd',
    ],

    'pagec' => [
        1 => '1-3页',
        2 => '4-9页',
        3 => '10-20页',
    ],

    'platformc' => [
        1 => 'amazon.com'
    ],

    'editor_menu' => [
        'source',
        '|',
        'bold',
        'underline',
        'italic',
        'strikethrough',
        'eraser',
        'forecolor',
        'bgcolor',
        '|',
        'quote',
        'fontfamily',
        'fontsize',
        'head',
        'unorderlist',
        'orderlist',
        'alignleft',
        'aligncenter',
        'alignright',
        '|',
        'link',
        'unlink',
        'table',
        'emotion',
        '|',
        'img',
        'video',
        'location',
        'insertcode',
        '|',
        'undo',
        'redo',
        'fullscreen',
    ],
];