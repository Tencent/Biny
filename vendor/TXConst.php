<?php
/**
 * 配置读取类
 */
class TXConst
{
    const BEE_TOOLKEY = "spaECBvfN2N8Lc8jfGB4";
    const BEE_FILEKEY = '5a80bb0d07dd1e99';
    const BEE_APIKEY ='w9Eb9aPik0tE6steeEy2';
    const second = 1;
    const minute = 60;
    const hour = 3600;
    const day = 86400;
    const week = 604800;
    const month = 2592000;
    const delete_status = 4;//各种删除状态的标志

    //配置常量
    const Api_PAGE = 10;
    const MAX_PAGE = 30;        // 翻页空间的每页最大个数

    const GOOD_APPRAISE= 1;     // 好评和差评,数值
    const BAD_APPRAISE = 2;

    const STATUS_OPEN = 1;      // 游戏和数据源的状态判断
    const STATUS_CLOSED = 0;
    const STATUS_DELETE = -1;

    const Demo_Project = 10000;

    const DEFAULT_COMMENT_NAME = '无名';

    const TOP_NUM = 5;      // 最近浏览，最热等个数

    const SHORT_CACHE = 500;  // 短时间缓存数据时间长度

    const BBS_TYPE = 1;   // BBS的类型值
    const APP_TYPE = 2;   // App的类型值 包括苹果和安卓
    const WB_TYPE = 3;    // 微博类型的值 包含微博和facebook

    const APPSTORE_TYPE = 1;   //子类
    const FACEBOOK_TYPE = 1;   //子类

    const ALL_GAMES_NAMES_KEY = 'BEE_ALL_GAME_NAMES_KEY';

    const APP_SCORE_COST = 30; // 积分扣除标准
    const BBS_SCORE_COST = 30;
    const CRON_SCORE_COST = 30;

    const HOT_WORDS_COUNT = 15;

    const DEFAULT_GAME_ICON = 'defaultgame.png';
    const DEFAULT_TYPE_ICON = 'defaulttype.png';

    const PLATFORM_APPLICATION_NEW = 0;
    const PLATFORM_APPLICATION_AGREE = 1;
    const PLATFORM_APPLICATION_DENY = 2;
    const PLATFORM_APPLICATION_REMOVE = 3;
    const PLATFORM_APPLICATION_MODIFY = 4;

    const EMOTION_COUNT = 5;
    const MAX_SEARCH_COUNT = 5;

    const PAY_STATUS_FAILED = 1;
    const PAY_STATUS_DONE = 0;
    const PAY_STATUS_WITHDRAW = 2;

    const NEG_SENTI = -1;
    const POS_SENTI = 1;
    const ANALYSIS_PAGE = 10;
    const ANALYSIS_MAX_KEYWORDS = 40;

    const MAIN_TYPE_GAME = 1;
    const MAIN_TYPE_APP = 2;

    const TOP_HOT_TITLES_NUM = 15;

    const MAX_TITLE = 20;

    const  MAX_RECENT_SEARCHWORDS = 5;

    const APPLY_ING = 0;
    const APPLY_ACCEPTRD = 1;
    const APPLY_DENYED = 2;
    const APPLY_MODIFY = 3;

    const TYPE_ICON_SIZE = 58;
    const GAME_ICON_SIZE = 96;

    const ALL_DATA_MAIN_POST = 1; // 主帖
    const ALL_DATA_ALL_POST = 2; // 非主帖
    const ALL_DATA_APP = 3; // 应用商店

    const MAX_KEYWORDS_LENGTH = 1000;

//    const TITLE_INDEX = '产品舆情 - WeTest腾讯质量开放平台 - 专注游戏，提升品质';
    const TITLE_INDEX = '产品舆情 - WeTest腾讯质量开放平台';
    const TITLE_SUPERCELL_INDEX = '舆情监控 - WeTest腾讯质量开放平台';
    const TITLE_GAME_LIST = '游戏列表 - 产品概况 - '.self::TITLE_INDEX;
    const TITLE_APP_LIST = '应用列表 - 产品概况 - '.self::TITLE_INDEX;
    const TITLE_GAMERANK_OUTLINE = 'iOS榜单概况 - '.self::TITLE_INDEX;
    const TITLE_GAMERANK_DETAIL = 'iOS榜单详情 - '.self::TITLE_INDEX;
    const TITLE_OVERVIEW = 'iOS榜单 - '.self::TITLE_INDEX;
    const TITLE_COLLECTION = '我的收藏 - '.self::TITLE_INDEX;
    const TITLE_INFOPUSH = '我的信息推送 - '.self::TITLE_INDEX;

    const TITLE_NEW_KEYWORD_PUSH = '新建关键词评论推送 - '.self::TITLE_INDEX;
    const TITLE_EDIT_KEYWORD_PUSH = '编辑关键词评论推送 - '.self::TITLE_INDEX;

    const TITLE_NEW_POST_PUSH = '新建热门帖子推送 - '.self::TITLE_INDEX;
    const TITLE_EDIT_POST_PUSH = '编辑热门帖子推送 - '.self::TITLE_INDEX;

    const TITLE_NEW_ALERT_PUSH = '新建关键词报警 - '.self::TITLE_INDEX;
    const TITLE_EDIT_ALERT_PUSH = '编辑关键词报警 - '.self::TITLE_INDEX;

    const TITLE_NEW_APP_PUSH = '新建AppStore数据推送 - '.self::TITLE_INDEX;
    const TITLE_EDIT_APP_PUSH = '编辑AppStore数据推送 - '.self::TITLE_INDEX;

    const TITLE_APP_ALERT_PUSH = 'AppStore数据报警 - '.self::TITLE_INDEX;

    const TITLE_ADMIN_CONFIG = '管理员配置 - '.self::TITLE_INDEX;
    const TITLE_PRODUCT_CONFIG = '产品配置 - 管理员配置 - '.self::TITLE_INDEX;
    const TITLE_APPLY_CONFIG = '申请配置 - 管理员配置 - '.self::TITLE_INDEX;
    const TITLE_ENTITY_CONFIG = '申请数据源 - 管理员配置 - '.self::TITLE_INDEX;
    const TITLE_TYPE_CONFIG = '分类配置 - 管理员配置 - '.self::TITLE_INDEX;
    const TITLE_HOTWORD_CONFIG = '热词词库 - 管理员配置 - '.self::TITLE_INDEX;
    const TITLE_CATEGORY_CONFIG = '渠道配置 - 管理员配置 - '.self::TITLE_INDEX;
    const TITLE_WORD_CONFIG = '分词词库 - 管理员配置 - '.self::TITLE_INDEX;
    const TITLE_MONITOR_CONFIG = '程序监控 - 管理员配置 - '.self::TITLE_INDEX;
    const TITLE_SEARCH_RESULT = '搜索结果 - '.self::TITLE_INDEX;
    const TITLE_GAME_APPLY = '申请添加新产品 - '.self::TITLE_INDEX;
    const TITLE_ENTITY_APPLY = '申请添加数据源 - %s - '.self::TITLE_INDEX;
    const TITLE_GAME_OUTLINE = '%s - 产品概况 - '.self::TITLE_INDEX;
    const TITLE_DATATYPE_OVERVIEW = '%s - 游戏风向标 - '.self::TITLE_INDEX;
    const TITLE_DATAGAMEDIM_OVERVIEW = '%s - %s - 游戏风向标 - '.self::TITLE_INDEX;
    const TITLE_GAME_OVERVIEW = '%游戏名称%  - 游戏风向标 - '.self::TITLE_INDEX;

    const STEP_INDEX = 1;               //首页引导
    const STEP_USER_CRON = 10;          //推送引导
    const STEP_NO_INTERRUPT = 100;      //免打扰引导

    const MONTH_CATEGOYR_LINE = 90;
    const WEEK_CATEGORY_LINE = 31;

    const CONTENT_GROUP_TYPE_MAIL = 0;
    const CONTENT_GROUP_TYPE_MAIL_WORDS = 1;
    const CONTENT_GROUP_TYPE_MAX = 2;
    const CONTENT_GROUP_MAX_COUNT = 5;

    const CRON_KEY  = 'beecronpay';
    const CRON_KEY_POST  = 'beecronpaypost';
    const CRON_KEY_ALERT  = 'beecronpayalert';

    const MAX_APPLY_PER_DAY = 5;

    const APPSTORE_LIST_FREE = 1;
    const APPSTORE_LIST_PAY = 2;
    const APPSTORE_LIST_HOT = 3;

    const APPSTORE_CHINA = 0;//中国
    const APPSTORE_AMERICA = 1;//美国
    const APPSTORE_GERMANY = 2;//德国

    const APPSTORE_GAME_TOTAL_LIST = 15;
    const APPSTORE_TOTAL_LIST = 0;

    const APPSTORE_RANGE_CHOICE = 1500;

    const ALLTYPEGAMES_PAGE = 200;

}