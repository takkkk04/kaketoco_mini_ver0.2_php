<?php
// =============================================
//カンマ区切りの文字列を配列で返す関数
// =============================================
function parse_list($str) {
    //trim()は前後の空白を削除する
    $str = trim((string)$str);
    if ($str === "") return [];
    //array_map()は配列の各要素にtrimを適用する
    //explode()は、$strを,で区切って配列にしろ
    $parts = array_map('trim', explode(',',$str));
    //array_filter(A,B)はAの中のBだけ残せ
    //fn($v) => $v !== "" はアロー関数で、$vが空文字じゃないときだけ返せ
    //array_values()は配列のキーを0から振り直す
    return array_values(array_filter($parts, fn($v) => $v !== ""));
}

//このページがPOST送信のとき（HTMLでmethod="POST"なら）
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = [
        //HTMLのinput name="number"を見ている、=>は連想配列（JSのオブジェクト）
        //三項演算子… 条件 ? A : B (条件がtrueならA、falseならB)
        //null合体演算子… A ?? B (Aが存在すればA、なければB)
        //→""じゃなければ → (int)は整数に変換
        "number" => ($_POST["number"] ?? "") !== "" ? (int)$_POST["number"] : null,
        "name" => trim($_POST["name"] ?? ""),
        "category" => $_POST["category"] ?? "",
        "crop" => parse_list($_POST["crop"] ?? ""),
        "target" => parse_list($_POST["target"] ?? ""),
        "interval" => ($_POST["interval"] ?? "") !== "" ? (int)$_POST["interval"] : null,
        "magnification" => ($_POST["magnification"] ?? "") !== "" ? (int)$_POST["magnification"] : null,
        "times" => ($_POST["times"] ?? "") !== "" ? (int)$_POST["times"] : null,
        "score" => ($_POST["score"] ?? "") !== "" ? (int)$_POST["score"] : null,
        "shopify_id" => trim($_POST["shopify_id"] ?? "") ?: null,
    ];

    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カケトコ_mini_管理者画面</title>
    <link rel="stylesheet" href="./css/reset.css">
    <link rel="stylesheet" href="./css/admin.css">
</head>

<body>
    <h1>カケトコmini 農薬マスタ管理</h1>
    <form id="pesticide_form" method="POST">
        <div>
            <label for="number">number(登録番号)</label>
            <input type="number" id="number" name="number">
        </div>

        <div>
            <label for="name">name(農薬名)</label>
            <input type="text" id="name" name="name">
        </div>

        <div>
            <label for="category">category(カテゴリ)</label>
            <select id="category" name="category">
                <option value="殺虫剤">殺虫剤</option>
                <option value="殺菌剤">殺菌剤</option>
                <option value="除草剤">除草剤</option>
            </select>
        </div>

        <div>
            <label for="crop">crop(作物名)<br>
                <small>かんきつ,りんご,等</small>
            </label>
            <input type="text" id="crop" name="crop" placeholder="カンマ区切り">
        </div>

        <div>
            <label for="target">target(病害虫)<br>
                <small>アブラムシ,コナジラミ,等</small>
            </label>
            <input type="text" id="target" name="target" placeholder="カンマ区切り">
        </div>

        <div>
            <label for="interval">interval(収穫前日数)</label>
            <input type="number" id="interval" name="interval" min="0">
        </div>

        <div>
            <label for="magnification">magnification(希釈倍率)</label>
            <input type="number" id="magnification" name="magnification" min="0">
        </div>

        <div>
            <label for="times">times(使用回数)</label>
            <input type="number" id="times" name="times" min="0">
        </div>

        <div>
            <label for="score">score(カケトコスコア)</label>
            <input type="number" id="score" name="score" min="0" max="100">
        </div>

        <div>
            <label for="shopify_id">shopify_id(Shopify_ID)</label>
            <input type="text" id="shopify_id" name="shopify_id" placeholder="hogehoge_flowable">
        </div>

        <button type="submit" id="save_btn">保存</button>

    </form>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="module" src="admin.js"></script>

</body>

</html>