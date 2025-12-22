// =============================================
// +++++++++++++++++++++++++++++++++++++++++++++
// shopify SDK連携（SDK＝API＋いろいろな機能）
// +++++++++++++++++++++++++++++++++++++++++++++
// =============================================

//ドメイン＝住所
const SHOPIFY_DOMAIN = "xn-lckmg7f.myshopify.com";
//shopifyのAPIキー（公開キーだからいちいち消さなくていい）
const SHOPIFY_STOREFRONT_TOKEN = "bc25ee7aec5d0c9de8b934c6f8e0aa90";

let shopifyUI = null;

// =============================================
// ShopifyのSDKを読み込む関数
// SDKとUIは別々に読み込まれるので、両方読み込まれるまで待つ
// =============================================

function loadShopifyBuySDK() {
    return new Promise((resolve, reject) => {
        const scriptURL = "https://sdks.shopifycdn.com/buy-button/latest/buy-button-storefront.min.js";

        //SDKとUIが読み込まれていればOK
        if (window.ShopifyBuy && window.ShopifyBuy.UI) {
            resolve();
            return;
        }

        //SDKだけ読み込まれていたら、UIの読み込みを待つ          
        if (window.ShopifyBuy && !window.ShopifyBuy.UI) {
            const t = setInterval(() => {
                if (window.ShopifyBuy.UI) {
                    clearInterval(t);
                    resolve();
                }
            },50);  //0.5秒ごとにUIが読み込まれたかチェック

            //10秒経ったらタイムアウト
            setTimeout(() => {
                clearInterval(t);
                reject(new Error("ShopifyBuy.UIが読み込めませんでした"));
            },10000);
            return;
        }
        
        //まだなにも読み込まれてない時、SDKの読み込みをしろ
        //まずscriptタグを作れ
        const script = document.createElement("script");
        script.async = true;
        //URLを読みにいけ
        script.src = scriptURL;
        //読み込めたらresolve、失敗したらreject
        script.onload = () => resolve();
        script.onerror = () => reject(new Error("ShopifyBuy SDKの読み込みに失敗しました"));
        //HTMLのheadかbodyにscriptタグをぶちこめ
        (document.head || document.body).appendChild(script);
    });
}

// =============================================
// ShopifyのUIを準備する関数
// =============================================
async function prepareShopifyUI() {
    //UIがすでにあったらそれを返す
    if (shopifyUI) return shopifyUI;
    //SDKとUIがちゃんと読み込まれるまで待つ
    await loadShopifyBuySDK();
    //shopifyに繋ぐ（クライアントを作る）
    const client = window.ShopifyBuy.buildClient({
        domain: SHOPIFY_DOMAIN,
        storefrontAccessToken: SHOPIFY_STOREFRONT_TOKEN,
    });
    //UIが準備できるまで待つ
    shopifyUI = await window.ShopifyBuy.UI.onReady(client);
    return shopifyUI;
}

// =============================================
// shopifyのデータを表示する関数
// =============================================
async function renderBuyButton (node, productID) {
    // productIDがなければ無視
    if (!productID) return;
    //上で作ったUI準備関数を呼ぶ
    const ui = await prepareShopifyUI();

    //UIのコンポーネントを作る
    ui.createComponent("product", {
        //shopifyの商品IDごとに取ってこい
        id: String(productID),
        node,
        moneyFormat: "%7B%7Bamount_no_decimals%7D%7D%E5%86%86",
        options: {
            product: {
                iframe: false,
                contents: {
                    img: true,
                    title: false,
                    price: true,
                },
                text: {button: "購入"},
            },
            cart: {
                text: {
                    total: "小計",
                    button: "購入手続きへ",
                }
            },
        },
    });
}

async function mountAllShopify() {
    const nodes = document.querySelectorAll(".shopify_cell");
    for (const node of nodes) {
        const productId = node.dataset.productId;
        await renderBuyButton(node, productId);
    }
}

$(function () {
    mountAllShopify().catch((e) => {
        console.error(e);
        alert("Shopifyの表示に失敗しました");
    });
});