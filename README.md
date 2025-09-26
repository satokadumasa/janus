# ■Nuxt3でマルチログインの実装についての注意点
- Laravel APIの実装に関して
- config/auth.phpのauth.defaults.guardでweb以外の値に書き換えること。
- #auth,guardsからwebの項目は削除すること。
- #app/Middlewareにsession.cookieを書き換えるMiddlewareを作成すべし。
- routes/api.phpの記述はサンプルソースの通り行うこと。
- 実装時にはSSL環境下で行うと色々と面倒事が回避できる。
　　具体的にはHTTP環境下だとXSRF-TOKENなどクッキーやトークンが
　　付加されずAPIがうまく動かないのでSSL環境下で実装をしていった方が良い。

# ■FRONTの実装に関して
- サンプルソースはそのままでは動かない。具体的に言うと、@qirolab/nuxt-sanctum-authentication
　　の一部モジュールに手を入れないと正常に動作しない。
- #node_modules/@qirolab/nuxt-sanctum-authentication/dist/runtime/helpers/utilities.jsは下記のように書き換えること。
``` bash
export function extractNestedValue(response, wrapperKey) {
  return response;　// <-　これを追記
  if (!wrapperKey) return response;
  return wrapperKey.split(".").reduce((acc, key) => acc && acc[key], response);
}
``` 
　- あとはサンプルコードの通りに実装するとうまく動くはずです。

