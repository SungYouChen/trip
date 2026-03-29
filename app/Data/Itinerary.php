<?php

namespace App\Data;

class Itinerary
{
    public static function getFlightInfo()
    {
        return [
            'price' => '$24,000',
            'airline' => '星宇航空 (Starlux)',
            'baggage' => '手提7kg / 托運23kg',
            'outbound' => [
                'date' => '12/28 (日)',
                'time' => '07:00 - 10:30',
                'route' => '台北 (TPE) ➝ 神戶 (UKB)',
                'flight_no' => 'JX???' // User didn't provide, leave generic or omit
            ],
            'inbound' => [
                'date' => '1/8 (四)',
                'time' => '14:00 - 16:15',
                'route' => '關西 (KIX) ➝ 台北 (TPE)',
            ]
        ];
    }

    public static function getShoppingList()
    {
        return [
            '藥妝' => [
                'OS Drug 最便宜藥妝店 (森之宮店)',
                '擦臉毛孔變小的',
                '刮鬍刀',
                'DHC 各種瘦身用藥',
                'Canmake腮紅 (凍紅的肌膚色)',
                'Revlon藍色蜜粉'
            ],
            '食物' => [
                'UHA 鐵軟糖',
                '昆布絲',
                '鹽昆布',
                '新鮮草莓 (熊本/菊池)',
                'frantz 魔法壺布丁',
                'Lawson 可麗露',
                '特殊版星巴克 (焙茶拿鐵)'
            ],
            '衣物' => [
                'UQ 好看內衣',
                '互相搭配的一套衣服'
            ]
        ];
    }

    public static function getSpotList()
    {
        return [
            'Kyoto' => [
                'SNOOPY茶屋 京都錦市場',
                '伏見稻荷大社',
                '御髮神社',
                '下鴨神社 (四季御守)',
                '河合神社 (鏡子御守)',
                '貴船半日遊 (貴船神社)',
                '京都嵐山',
                '清水寺/特色星巴克',
                '八坂神社',
                '藍瓶 京都店',
                '錦市場',
                '藝妓街',
                '海の京都 (？)'
            ],
            'Osaka' => [
                '難波 / 難波八阪神社',
                '大国主神社 (老鼠招財)',
                '大鳥大社 (透明御守？)',
                '勝尾寺 (達摩)',
                '太陽之塔 / 萬博',
                '環球影城',
                '黑門市場',
                '道頓堀 / 心齋橋',
                '海遊館 (？)',
                '阿倍野 HARUKAS',
                '木津市場',
                '通天閣',
                '梅田 (扭蛋之森)',
                '中崎町'
            ]
        ];
    }

    public static function getAll()
    {
        return [
            // [
            //     'date' => '12/26',
            //     'day' => '五',
            //     'title' => '測試行程 (Today)',
            //     'location' => 'Home',
            //     'summary' => '測試時間軸變色功能',
            //     'accommodation' => null,
            //     'schedule' => [
            //         ['time' => '09:00', 'activity' => '已經過去的行程 (09:00)', 'note' => '應該是灰色的'],
            //         ['time' => '10:00 – 15:00', 'activity' => '現在進行中 (10:00-15:00)', 'note' => '應該是亮起且呼吸燈'],
            //         ['time' => '16:00', 'activity' => '未來的行程 (16:00)', 'note' => '應該是灰色的'],
            //         ['time' => '23:59', 'activity' => '未來的行程 (23:59)', 'note' => '應該是灰色的'],
            //     ],
            // ],
            [
                'date' => '12/28',
                'day' => '日',
                'title' => '神戶',
                'location' => 'Kobe',
                'summary' => '抵達神戶，三宮逛街，神戶牛晚餐',
                'accommodation' => [
                    'name' => 'Hotel Casabella Inn Kobe',
                    'address' => 'Nishikamitachibanadori, 1 Chome−4−27 Casabella Inn Kobe, 1F, 兵庫區, 652-0033 神戶市, 兵庫縣, 日本',
                    'check_in' => '15:00',
                    'price' => '$2,277',
                    'note' => '神戶1晚',
                ],
                'schedule' => [
                    ['time' => '07:00 - 10:30', 'activity' => '✈️ 去程：台北(TPE) - 神戶(UKB)', 'note' => '星宇航空'],
                    ['time' => '10:30 - 11:10', 'activity' => 'UKB 抵達 → 三宮', 'note' => '', 'map_query' => 'Sannomiya Station'],
                    ['time' => '11:10', 'activity' => 'Casa Bella Inn Kobe 放行李', 'note' => ''],
                    ['time' => '12:00', 'activity' => '三宮午餐？', 'note' => ''],
                    ['time' => '13:10', 'activity' => '元町商店街', 'note' => ''],
                    ['time' => '14:40', 'activity' => '⛩ 生田神社', 'note' => ''],
                    ['time' => '15:30', 'activity' => '三宮附近喝飲料？', 'note' => ''],
                    ['time' => '16:00', 'activity' => '回飯店休息', 'note' => ''],
                    ['time' => '17:30', 'activity' => '三宮夜逛 神戶港 或 異人館街', 'note' => ''],
                    ['time' => '19:00', 'activity' => '🥩 Kobe Steak Ishida 晚餐', 'note' => '預算 $4,800'],
                ],
            ],
            [
                'date' => '12/29',
                'day' => '一',
                'title' => '神戶 → 京都｜清水寺・祇園',
                'location' => 'Kyoto',
                'summary' => '經典京都第一次接觸',
                'accommodation' => [
                    'name' => 'Cocoon Kyoto Guest House',
                    'address' => '7-16 Ichinohashimiyanouchicho, Higashiyama Ward, Kyoto, 605-0994日本',
                    'check_in' => '16:00',
                    'price' => '$16,092 (4晚)',
                    'note' => '京都4晚，附近有自助洗衣店',
                ],
                'schedule' => [
                    ['time' => '08:30', 'activity' => '神戶出發 → 京都', 'note' => '', 'map_query' => 'Kyoto Station'],
                    ['time' => '10:30', 'activity' => '寄放行李（京都住宿）', 'note' => ''],
                    ['time' => '11:00 – 12:30', 'activity' => '⛩ 清水寺', 'note' => ''],
                    ['time' => '12:30 – 13:30', 'activity' => '🍽 清水坂午餐', 'note' => '建議：湯豆腐 / 蕎麥麵（路邊選）'],
                    ['time' => '13:30 – 15:00', 'activity' => '清水坂・二年坂・三年坂', 'note' => ''],
                    ['time' => '15:10 – 15:40', 'activity' => '☕ 清水寺星巴克', 'note' => ''],
                    ['time' => '16:10 – 16:40', 'activity' => '⛩ 八坂神社', 'note' => ''],
                    ['time' => '17:00 – 18:00', 'activity' => '祇園、藝妓街散步', 'note' => ''],
                    ['time' => '晚餐', 'activity' => 'Gion Duck Rice (三選一)', 'note' => ''],
                    ['time' => '20:00', 'activity' => '回住宿', 'note' => ''],
                ],
            ],
            [
                'date' => '12/30',
                'day' => '二',
                'title' => '嵐山一日遊',
                'location' => 'Kyoto',
                'summary' => '竹林小徑 + 渡月橋',
                'accommodation' => [
                    'name' => 'Cocoon Kyoto Guest House',
                    'address' => '7-16 Ichinohashimiyanouchicho, Higashiyama Ward, Kyoto, 605-0994日本',
                ],
                'schedule' => [
                    ['time' => '09:30 - 10:30', 'activity' => '嵐山小火車（買票？）或一般JR電車', 'note' => '', 'map_query' => 'Saga-Arashiyama Station'],
                    ['time' => '10:40 - 12:00', 'activity' => '⛩️ 竹林小徑 + 御髮神社', 'note' => ''],
                    [
                        'time' => '12:00 - 13:30',
                        'activity' => '🍽️ 午餐',
                        'note' => '蕎麥麵 嵐山よしむら 或 廣川鰻魚飯',
                        'sub_activities' => [
                            '蕎麥麵 嵐山よしむら',
                            'うなぎ屋 廣川鰻魚飯 (需先訂位ＸＸＸ)'
                        ]
                    ],
                    [
                        'time' => '13:30 - 16:30',
                        'activity' => '🌉 渡月橋散步',
                        'note' => '小吃巡禮',
                        'sub_activities' => [
                            '8108kyoto arashiyama/八十八良葉舎嵐山🍵',
                            'Bread,Espresso&嵐山庭園（咖啡、吐司）',
                            '京豆庵（豆腐冰淇淋）',
                            '% ARABICA 京都嵐山店',
                            '中村屋炸肉餅',
                            '嵯峨野可樂餅'
                        ]
                    ],
                    ['time' => '16:30 - 17:30', 'activity' => '☕️ 喝咖啡休息', 'note' => 'Yojiya Cafe 藝妓圖案咖啡'],
                    ['time' => '晚上', 'activity' => '回市區逛逛走走吃吃', 'note' => ''],
                ],
            ],
            [
                'date' => '12/31',
                'day' => '三',
                'title' => '伏見稻荷＋下鴨神社＋八阪神社跨年',
                'location' => 'Kyoto',
                'summary' => '能量神社＋京都式跨年',
                'accommodation' => [
                    'name' => 'Cocoon Kyoto Guest House',
                    'address' => '7-16 Ichinohashimiyanouchicho, Higashiyama Ward, Kyoto, 605-0994日本',
                ],
                'schedule' => [
                    ['time' => '08:00 – 10:00', 'activity' => '⛩ 伏見稻荷大社', 'note' => '早去避人潮'],
                    ['time' => '10:30', 'activity' => '返回市區', 'note' => ''],
                    [
                        'time' => '12:00',
                        'activity' => '🍽 午餐（河原町／出町柳一帶）',
                        'note' => '建議',
                        'sub_activities' => [
                            '炭燒鰻 土井活鰻',
                            '古民家カフェこむすび'
                        ]
                    ],
                    ['time' => '13:30 – 15:30', 'activity' => '⛩ 下鴨神社（四季御守）/ 河合神社（鏡子御守）', 'note' => ''],
                    ['time' => '16:00', 'activity' => '回住宿休息', 'note' => '很重要！'],
                    ['time' => '18:00', 'activity' => '早一點吃晚餐（祇園／河原町）', 'note' => '建議：居酒屋、定食、烏龍麵'],
                    ['time' => '20:00', 'activity' => '回住宿保暖、穿厚一點', 'note' => ''],
                    ['time' => '21:30', 'activity' => '出發前往八阪神社', 'note' => ''],
                    ['time' => '22:00 – 00:30', 'activity' => '🎉 八阪神社跨年', 'note' => '參拜、敲鐘、拍夜景'],
                    ['time' => '00:30 – 01:00', 'activity' => '回住宿休息 💤', 'note' => ''],
                    ['time' => '✅ 小提醒', 'activity' => '重要事項', 'note' => '晚上超冷(比白天冷)、穿好走的鞋、帶零錢(香油錢)、行動電源'],
                ],
            ],
            [
                'date' => '1/1',
                'day' => '四',
                'title' => '貴船半日＋四條錦市場',
                'location' => 'Kyoto',
                'summary' => '自然＋吃',
                'accommodation' => [
                    'name' => 'Cocoon Kyoto Guest House',
                    'address' => '7-16 Ichinohashimiyanouchicho, Higashiyama Ward, Kyoto, 605-0994日本',
                ],
                'schedule' => [
                    ['time' => '08:30', 'activity' => '住宿出發', 'note' => ''],
                    ['time' => '09:10', 'activity' => '出町柳站 → 叡山電車 → 貴船口站', 'note' => '', 'map_query' => 'Kibuneguchi Station'],
                    ['time' => '09:50', 'activity' => '貴船口站 → 巴士/步行 → 貴船神社', 'note' => ''],
                    ['time' => '10:10 – 11:30', 'activity' => '⛩ 貴船神社參拜', 'note' => '拍照、水占卜'],
                    ['time' => '11:45', 'activity' => '返回', 'note' => ''],
                    ['time' => '13:00', 'activity' => '抵達市區（河原町／四條）', 'note' => ''],
                    [
                        'time' => '13:10 – 14:30',
                        'activity' => '🍢 錦市場',
                        'note' => '吃吃喝喝',
                        'sub_activities' => [
                            'SNOOPY 茶屋',
                            '百年壽喜燒（看排隊決定）',
                            'koé donuts'
                        ]
                    ],
                    [
                        'time' => '14:40 – 15:30',
                        'activity' => '🛍 四條通逛街',
                        'note' => '逛街',
                        'sub_activities' => [
                            '蔦屋書店 (J-Scent 香水)',
                            'STUSSY'
                        ]
                    ],
                    ['time' => '16:30', 'activity' => '六角藍瓶／I\'m donut 休息', 'note' => ''],
                    ['time' => '17:30', 'activity' => '壽司郎', 'note' => ''],
                    ['time' => '19:00', 'activity' => '回住宿休息', 'note' => ''],
                    ['time' => '✅ 備註', 'activity' => '交通與天氣', 'note' => '叡山電車刷IC卡即可 / 貴船冬天超冷，手套圍巾必備'],
                ],
            ],
            [
                'date' => '1/2',
                'day' => '五',
                'title' => '京都 → 大阪梅田',
                'location' => 'Osaka',
                'summary' => '移動日，梅田逛街',
                'accommodation' => [
                    'name' => 'b&難波4號店 A 館',
                    'address' => '557-0021 大阪府大阪市西成区北開１丁目２−25',
                    'check_in' => '16:00',
                    'price' => '$10,348 (6晚)',
                    'note' => '大阪6晚，可洗衣服',
                ],
                'schedule' => [
                    ['time' => '09:30', 'activity' => '京都出發（京 TRAIN 雅洛 10:40）', 'note' => '京都河原町 -> 大阪梅田', 'map_query' => 'Osaka-Umeda Station'],
                    ['time' => '11:00', 'activity' => '梅田寄放行李', 'note' => ''],
                    [
                        'time' => '11:30',
                        'activity' => '🍽 午餐（中崎町）',
                        'note' => '中崎町',
                        'sub_activities' => [
                            'Onigiri Gorichan',
                            'OSA COFFEE'
                        ]
                    ],
                    ['time' => '下午', 'activity' => '中崎町走走逛逛', 'note' => ''],
                    [
                        'time' => '15:00 – 18:00',
                        'activity' => '梅田逛逛買買',
                        'note' => '梅田',
                        'sub_activities' => [
                            '扭蛋之森',
                            '可麗露 Daniel'
                        ]
                    ],
                    ['time' => '19:00', 'activity' => '回住宿', 'note' => ''],
                ],
            ],
            [
                'date' => '1/3',
                'day' => '六',
                'title' => '大阪市區經典',
                'location' => 'Osaka',
                'summary' => '難波～通天閣',
                'accommodation' => [
                    'name' => 'b&難波4號店 A 館',
                    'address' => '557-0021 大阪府大阪市西成区北開１丁目２−25',
                ],
                'schedule' => [
                    ['time' => '09:30', 'activity' => '大國主神社', 'note' => ''],
                    ['time' => '10:30', 'activity' => '🍽 海鮮丼専門店 木津 魚市食堂', 'note' => ''],
                    ['time' => '12:00', 'activity' => '難波八阪神社', 'note' => ''],
                    ['time' => '下午茶', 'activity' => 'Vourke', 'note' => ''],
                    ['time' => '下午', 'activity' => '難波百貨', 'note' => ''],
                    ['time' => '16:30', 'activity' => '通天閣', 'note' => ''],
                    ['time' => '18:00', 'activity' => '晚餐', 'note' => 'Tora no Sumika？或通天閣再吃'],
                ],
            ],
            [
                'date' => '1/4',
                'day' => '日',
                'title' => '勝尾寺＋太陽之塔（重點日✨）',
                'location' => 'Osaka',
                'summary' => '萬博紀念公園',
                'accommodation' => [
                    'name' => 'b&難波4號店 A 館',
                    'address' => '557-0021 大阪府大阪市西成区北開１丁目２−25',
                ],
                'schedule' => [
                    ['time' => '08:30', 'activity' => '出發', 'note' => ''],
                    ['time' => '10:00 – 12:00', 'activity' => '⛩ 勝尾寺', 'note' => ''],
                    ['time' => '12:30 – 13:30', 'activity' => '🍽 午餐（萬博附近）', 'note' => 'lalaport？'],
                    ['time' => '14:00 – 16:00', 'activity' => '萬博紀念公園＋太陽之塔', 'note' => '15:00 入場，門票 $373'],
                    ['time' => '18:00', 'activity' => '回市區晚餐（隨意）或旁邊 lalaport', 'note' => ''],
                ],
            ],
            [
                'date' => '1/5',
                'day' => '一',
                'title' => '心齋橋＋道頓堀',
                'location' => 'Osaka',
                'summary' => '逛街日',
                'accommodation' => [
                    'name' => 'b&難波4號店 A 館',
                    'address' => '557-0021 大阪府大阪市西成区北開１丁目２−25',
                ],
                'schedule' => [
                    ['time' => '12:00 – 13:00', 'activity' => '🍽 午餐（心齋橋）', 'note' => '拉麵 / 丼飯'],
                    [
                        'time' => '13:00 – 18:00',
                        'activity' => '心齋橋・道頓堀',
                        'note' => '逛街名單',
                        'sub_activities' => [
                            'The Flavor Design (平價香水店)',
                            'Ramen Hayashida (格力高人腳下拉麵)',
                            '雞湯拉麵 座銀 (鶏SOBA)',
                            '吉次牛舌',
                            'Sakae Sushi',
                            'CANELÉ du JAPON (可麗露)',
                            'Udon Kyutaro (人生烏龍麵)',
                            'Naniwa Omuraisu (浪花蛋包飯)'
                        ]
                    ],
                ],
            ],
            [
                'date' => '1/6',
                'day' => '二',
                'title' => '自由補洞日 🧡',
                'location' => 'Osaka',
                'summary' => '黑門市場 / OS Drug',
                'accommodation' => [
                    'name' => 'b&難波4號店 A 館',
                    'address' => '557-0021 大阪府大阪市西成区北開１丁目２−25',
                ],
                'schedule' => [
                    ['time' => '午餐', 'activity' => '黑門市場 (早去) 或 Sushi Sakaba Sashisu', 'note' => '鮮魚 魚豊 (阿婆的鰻魚飯)'],
                    ['time' => '下午', 'activity' => '心齋橋再逛 / OS Drug 掃貨', 'note' => ''],
                    ['time' => '晚餐', 'activity' => '🥩 燒肉力丸', 'note' => ''],
                    ['time' => '晚上', 'activity' => '阿倍野 HARUKAS 夜景', 'note' => ''],
                ],
            ],
            [
                'date' => '1/7',
                'day' => '三',
                'title' => '環球影城 USJ',
                'location' => 'Osaka',
                'summary' => 'USJ 一日遊 (門票+快速通關 $10,406)',
                'accommodation' => [
                    'name' => 'b&難波4號店 A 館',
                    'address' => '557-0021 大阪府大阪市西成区北開１丁目２−25',
                ],
                'schedule' => [
                    ['time' => '07:00', 'activity' => '搭車', 'note' => '今宮站 (御堂筋線) → 大國町轉車 → 西九条 → 環球城站', 'map_query' => 'Universal City Station'],
                    ['time' => '07:40', 'activity' => '抵達 USJ 排隊入場', 'note' => '早餐超商處理'],
                    ['time' => '09:00 – 10:40', 'activity' => '自由玩熱門設施／逛街拍照', 'note' => '建議：哈利波特區、蜘蛛人、小小兵商店'],
                    ['time' => '11:10 – 11:40', 'activity' => '小小兵瘋狂任務（快速通關）', 'note' => ''],
                    ['time' => '12:00 – 13:00', 'activity' => '午餐', 'note' => '小小兵漢堡 / 芝麻街 / 環球餐廳'],
                    ['time' => '13:20 – 14:20', 'activity' => '超級任天堂世界™ (入場時段)', 'note' => ''],
                    ['time' => '13:20 – 13:50', 'activity' => '瑪利歐賽車 (快速通關)', 'note' => ''],
                    ['time' => '14:00 – 15:00', 'activity' => '任天堂區自由逛', 'note' => '戳磚 / 點心 / 打卡'],
                    [
                        'time' => '15:00 之後',
                        'activity' => '自由玩其他設施 / 商店採買',
                        'note' => '大白鯊 (你快速4裡有包含)',
                        'sub_activities' => [
                            '侏羅紀公園',
                            '史努比 / 芝麻街',
                            '哈利波特禁忌之旅',
                            '★ 可玩項目: 瑪利歐賽車、耀西、大白鯊、哈利波特、侏羅紀、小小兵'
                        ]
                    ],
                    ['time' => '18:00 – 19:00', 'activity' => '晚餐', 'note' => '園區內 or 出園後 (一蘭/松屋)'],
                    ['time' => '19:00 – 20:00', 'activity' => '回今宮站', 'note' => '同路線返回'],
                ],
            ],
            [
                'date' => '1/8',
                'day' => '四',
                'title' => '返程',
                'location' => 'Osaka',
                'summary' => '關西機場 -> 台北',
                'accommodation' => null,
                'schedule' => [
                    ['time' => '10:00', 'activity' => '出發搭車', 'note' => '往關西機場', 'map_query' => 'Kansai International Airport'],
                    ['time' => '11:00', 'activity' => '抵達關西機場', 'note' => ''],
                    ['time' => '14:00 - 16:15', 'activity' => '✈️ 返程：關西(KIX) - 台北(TPE)', 'note' => '星宇航空'],
                ],
            ],
        ];
    }
}
