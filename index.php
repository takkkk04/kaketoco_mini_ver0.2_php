<?php
$filePath = __DIR__ . '/data/data.json';
//JSONを配列に変換する
$list = [];
if (file_exists($filePath)) {
    $json = file_get_contents($filePath);
    $decoded = json_decode($json, true);
    if (is_array($decoded)){
        $list = $decoded;
    }
}

//JSONから受け取る
$category = $_GET["category"] ?? "殺虫剤";
$crop = trim($_GET["crop"] ?? "");
$target = trim($_GET["target"] ?? "");

// =============================================
// 絞り込み、検索結果用の配列 $filtered を作る
// =============================================

//array_values()は配列のキーを0から振り直す
//array_filter(A,B)はAの中のBだけ残す
//use($aaa, $bbb)は外で定義した変数を関数の中で使えるようにする
$filtered = array_values(array_filter($list, function($p) use ($category, $crop, $target) {

    //カテゴリーが違ったらfalseを返す
    if (($p["category"] ?? "") !== $category) return false;

    // 作物が指定されていたらチェック
    if ($crop !== "") {
        //is_array()は配列かどうか調べる関数、配列ならtrue
        //三項演算子… 条件 ? A : B (条件がtrueならA、falseならB)
        //null合体演算子… A ?? B (Aが存在すればA、なければB)
        //配列じゃなかったら空配列で返す
        $crops = is_array($p["crop"] ?? null) ? $p["crop"] : [];
        //in_array(A,B,C)はBの中にAがあるか調べる、Cは厳密に調べる(trueなら型も見る)
        //!in_array()はBの中にAがなかったらtrue
        if (!in_array($crop, $crops, true)) return false;
    }

    if ($target !== "") {
        $targets = is_array($p["target"] ?? null) ? $p["target"] : [];
        if (!in_array($target, $targets, true)) return false;
    }

    return true;
}));

// =============================================
// スコア順にソートする
// =============================================

//usort(A,B)はAをBの条件でソートする
usort($filtered, function($a, $b){
    //スコアを整数でふたつ取り出す、なければ0
    $sa = (int)($a["score"] ?? 0);
    $sb = (int)($b["score"] ?? 0);
    //a <=> b 宇宙船演算子、aが小さいと-1、同じなら0、大きいと1を返す
    //b <=> a にすると降順ソートになる
    return $sb <=> $sa;
});

$count = count($filtered);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カケトコ_mini</title>
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <header class="app_header">
        <h1>カケトコ_mini</h1>
        <a href="./admin/admin.php" class="admin_link">管理画面へ</a>
    </header>

    <h2>登録データ一覧（テスト）</h2>
    <ul>
        <?php foreach ($list as $p): ?>
        <li>
            <?php echo htmlspecialchars($p["name"] ?? ""); ?>
            (<?php echo htmlspecialchars($p["category"] ?? ""); ?>)
            score: <?php echo htmlspecialchars((string)($p["score"] ?? "")); ?>
        </li>
        <?php endforeach; ?>
    </ul>

    <main class="app_main">
        <section class="search_section">
            <h2>検索条件</h2>

            <form id="search_form" method="GET" action="">
                <div class="form_row">
                    <label for="category">カテゴリ</label>
                    <select name="category" id="category">
                        <option value="殺虫剤">殺虫剤</option>
                        <option value="殺菌剤">殺菌剤</option>
                        <option value="除草剤">除草剤</option>
                    </select>
                </div>

                <div class="form_row">
                    <label for="crop">作物名</label>
                    <select name="crop" id="crop">
                        <option value="">指定なし</option>
                        <!-- ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝
                            あとで作物名プルダウンつくる
                        ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝ -->
                    </select>
                </div>

                <div class="form_row">
                    <label for="target">病害虫</label>
                    <select name="target" id="target">
                        <option value="">指定なし</option>
                        <!-- ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝
                            あとで病害虫プルダウンつくる
                        ＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝＝ --> 
                    </select>
                </div>

                <div class="form_row_btn">
                    <button type="submit" id="search_btn">検索</button>
                    <button type="reset" id="reset_btn">リセット</button>
                </div>

            </form>
        </section>

        <section class="result_section">
            
            <div class="result_header">
                <h2>検索結果</h2>
                <div class="result_meta">
                    <span id="result_count">0件</span>
                </div>
            </div>

            <!-- 検索結果表示エリア -->
            <div id="result_list" class="result_list"></div>

        </section>
    </main>

    

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- <script type="module" src="app.js"></script> -->
</body>
</html>