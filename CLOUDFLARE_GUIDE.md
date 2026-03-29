# Cloudflare Tunnel 設定教學

這是我們剛剛「自動化」設定 Cloudflare 的步驟記錄。
如果您之後想要新增其他子網域（例如 `wedding.elk-web.com`），可以照著這個流程做。

我是使用 **Command Line (終端機)** 直接跟 Cloudflare 溝通，而不是去網頁上點擊，這樣更快速且穩定。

## 核心步驟

### 1. 登入 (Login)
讓電腦獲得 Cloudflare 的控制權限。
```bash
cloudflared tunnel login
```
*(這步會跳出網頁讓您選網域)*

### 2. 建立通道 (Create Tunnel)
建立一條專屬的加密通道，並給它取個名字 (例如 `my-new-site`)。
```bash
cloudflared tunnel create my-new-site
```
*這一步會產生一個 Tunnel ID (亂碼) 和憑證檔。*

### 3. 設定 DNS (Route DNS) - **這就是「設定網址」的關鍵步驟**
告訴 Cloudflare：「請把 `abc.elk-web.com` 這個網址，指引到這條通道來」。
```bash
cloudflared tunnel route dns my-new-site abc.elk-web.com
```
*執行這行指令後，您去 Cloudflare 後台看，就會發現 DNS 紀錄已經自動加進去了！*

### 4. 撰寫設定檔 (Config)
告訴通道要把流量轉發給本機的哪個服務 (例如 `localhost:8000`)。
建立一個 `config.yml`：
```yaml
tunnel: <您的 Tunnel ID>
credentials-file: <憑證路徑>

ingress:
  - hostname: abc.elk-web.com
    service: http://localhost:8000
  - service: http_status:404
```

### 5. 啟動 (Run)
```bash
cloudflared tunnel --config config.yml run my-new-site
```

---

## 此次專案的設定值
- **網域**: `trip.elk-web.com`
- **通道名稱**: `japan-trip`
- **對應服務**: `http://localhost:8000` (您的 Laravel 網站)
