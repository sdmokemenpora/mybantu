# DOKUMEN PANDUAN PENGATURAN NGINX & WAZUH SIEM (DOCKER)
## Sistem Monitoring Keamanan Aplikasi Web MyBantu

- **Aplikasi:** MyBantu (Laravel 13, PHP 8.3, MySQL)
- **Stack Monitoring:** Docker Compose — Nginx Reverse Proxy (`nginx:alpine`) + Wazuh Single-Node `v4.9.2` (Manager, Indexer, Dashboard)
- **Lokasi Konfigurasi:** `docker/wazuh/`

---

## 1. Ringkasan Arsitektur & Struktur Direktori

Seluruh konfigurasi server-side monitoring disimpan secara terpusat di dalam folder `docker/wazuh/` sehingga bersifat portabel dan tidak perlu dikonfigurasi ulang setiap kali komputer dinyalakan ulang.

```text
mybantu/
├── bootstrap/app.php                                  # TrustProxies untuk meneruskan IP asli klien
├── config/logging.php                                 # Channel log 'security' & 'security_json'
├── storage/logs/security-json.log                     # Output log JSON Laravel (di-mount ke Wazuh)
└── docker/wazuh/
    ├── docker-compose.yml                             # Orkestrasi 4 container (Nginx + Wazuh Stack)
    ├── generate-indexer-certs.yml                     # Generator sertifikat TLS internal Wazuh
    ├── config/
    │   ├── certs.yml                                  # Daftar node untuk sertifikat TLS
    │   ├── nginx/
    │   │   └── default.conf                           # Konfigurasi Reverse Proxy & Real-IP Nginx
    │   ├── wazuh_cluster/
    │   │   ├── wazuh_manager.conf                     # Konfigurasi utama ossec.conf Wazuh Manager
    │   │   └── local_rules.xml                        # 7 Custom Rule ID (100101–100204, MITRE T1110)
    │   ├── wazuh_indexer/
    │   │   ├── wazuh.indexer.yml                      # Konfigurasi OpenSearch / Wazuh Indexer
    │   │   └── internal_users.yml                     # Kredensial internal Indexer
    │   ├── wazuh_dashboard/
    │   │   ├── opensearch_dashboards.yml              # Konfigurasi Dashboard UI
    │   │   └── wazuh.yml                              # Koneksi Dashboard -> Wazuh Manager API
    │   └── wazuh_indexer_ssl_certs/                   # (Generated, diabaikan di .gitignore)
    └── logs/nginx/                                    # Output access.log & error.log Nginx (diabaikan di .gitignore)
```

### Pemetaan Port Lokal

| Layanan | Container | Port Host | Port Container | Keterangan |
| :--- | :--- | :---: | :---: | :--- |
| Laravel App | *(Host Windows)* | `8000` | — | `php artisan serve --host=0.0.0.0 --port=8000` |
| Nginx Reverse Proxy | `wazuh-mybantu.nginx-1` | `8080` | `80` | Pintu masuk HTTP (`http://127.0.0.1:8080`) & target `ngrok http 8080` |
| Wazuh Dashboard | `wazuh-wazuh.dashboard-1` | `5601` | `5601` | UI Dashboard SIEM (`https://127.0.0.1:5601`) |
| Wazuh Indexer | `wazuh-wazuh.indexer-1` | `9200` | `9200` | Mesin penyimpanan & pencarian log (OpenSearch) |
| Wazuh Manager | `wazuh-wazuh.manager-1` | `1514`, `1515`, `55000` | `1514`, `1515`, `55000` | Analisis log, rule engine, Filebeat, & Wazuh API |

---

## 2. Pengaturan Nginx Reverse Proxy

### 2.1 File `docker/wazuh/config/nginx/default.conf`

```nginx
server {
    listen 80;
    server_name localhost;

    # Trust Docker bridge & ngrok proxy headers so access.log records the attacker's real public IP
    set_real_ip_from 172.16.0.0/12;
    set_real_ip_from 10.0.0.0/8;
    set_real_ip_from 192.168.0.0/16;
    real_ip_header X-Forwarded-For;
    real_ip_recursive on;

    access_log /var/log/nginx/access.log combined;
    error_log  /var/log/nginx/error.log warn;

    location / {
        proxy_pass http://host.docker.internal:8000;
        proxy_http_version 1.1;
        proxy_set_header Host              $http_host;
        proxy_set_header X-Real-IP         $remote_addr;
        proxy_set_header X-Forwarded-For   $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_redirect off;
    }
}
```

### 2.2 Penjelasan Setiap Direktif Nginx

| Direktif | Fungsi |
| :--- | :--- |
| `set_real_ip_from` (`172.16.0.0/12`, `10.0.0.0/8`, `192.168.0.0/16`) | Memberi tahu Nginx bahwa subnet internal Docker/lokal adalah proxy terpercaya. |
| `real_ip_header X-Forwarded-For;` & `real_ip_recursive on;` | Mengganti `$remote_addr` dengan **IP publik asli penyerang** (misal saat diakses lewat `ngrok`), bukan IP gateway Docker (`172.21.0.1`). |
| `access_log /var/log/nginx/access.log combined;` | Menulis seluruh request HTTP dalam format standar *combined* (IP, timestamp, method, URL, status code, referrer, User-Agent) yang kompatibel dengan decoder `web-accesslog` bawaan Wazuh. |
| `error_log /var/log/nginx/error.log warn;` | Mencatat error teknis web server dengan tingkat `warn` ke atas. |
| `proxy_pass http://host.docker.internal:8000;` | Meneruskan seluruh trafik masuk dari container Nginx ke server Laravel yang berjalan di host Windows (`port 8000`). |
| `proxy_set_header X-Real-IP / X-Forwarded-For` | Meneruskan IP asli klien ke aplikasi Laravel agar tercatat akurat di tabel `security_logs` dan `security-json.log`. |

---

## 3. Pengaturan Orkestrasi Docker (`docker/wazuh/docker-compose.yml`)

Berikut poin-poin pengaturan utama yang disesuaikan dari template resmi `wazuh/wazuh-docker v4.9.2`:

### 3.1 Service `wazuh.manager`
Memasang file konfigurasi, custom rules, dan folder log Nginx + Laravel secara *read-only* (`:ro`):
```yaml
    volumes:
      - ./config/wazuh_cluster/wazuh_manager.conf:/wazuh-config-mount/etc/ossec.conf
      - ./config/wazuh_cluster/local_rules.xml:/wazuh-config-mount/etc/rules/local_rules.xml
      - ./logs/nginx:/var/log/mybantu-nginx:ro
      - ../../storage/logs:/var/log/mybantu-app:ro
```

### 3.2 Service `wazuh.indexer` (Tuning Memori RAM Lokal)
```yaml
    environment:
      - "OPENSEARCH_JAVA_OPTS=-Xms768m -Xmx768m -XX:MaxDirectMemorySize=384m"
```
- **Alasan pengaturan ini:** Default `1g` terlalu berat untuk Docker Desktop dengan alokasi ~3.7 GB RAM, sedangkan `512m` membuat `MaxDirectMemorySize` otomatis menjadi `256m` yang menyebabkan error `OutOfMemoryError` pada `OpenSearchSecurityPlugin` (membutuhkan ~291 MB). Nilai **`768m` heap + `384m` direct memory** adalah titik stabil paling efisien.

### 3.3 Service `wazuh.dashboard`
```yaml
    ports:
      - "5601:5601"
```
- Dipetakan ke port `5601` (bukan `443`) agar tidak bentrok dengan layanan web/Laragon di Windows.
- **Kredensial default Dashboard:** Username `admin` / Password `SecretPassword`.

### 3.4 Service `mybantu.nginx`
```yaml
  mybantu.nginx:
    image: nginx:alpine
    hostname: mybantu.nginx
    restart: always
    ports:
      - "8080:80"
    extra_hosts:
      - "host.docker.internal:host-gateway"
    volumes:
      - ./config/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
      - ./logs/nginx:/var/log/nginx
```

---

## 4. Pengaturan Wazuh Manager (`wazuh_manager.conf`)

File `docker/wazuh/config/wazuh_cluster/wazuh_manager.conf` di-mount ke `/var/ossec/etc/ossec.conf` di dalam container `wazuh.manager`. Dua bagian penting yang dikonfigurasi adalah:

### 4.1 Optimasi Resource (`vulnerability-detection`)
Dinonaktifkan agar Wazuh tidak mengunduh & mengindeks feed CVE ratusan megabyte di lingkungan lokal:
```xml
  <vulnerability-detection>
    <enabled>no</enabled>
    <index-status>no</index-status>
    <feed-update-interval>60m</feed-update-interval>
  </vulnerability-detection>
```

### 4.2 Pengambilan File Log (`<localfile>`)
Menambahkan 3 target file log agar dipantau secara *real-time* oleh daemon `wazuh-logcollector`:
```xml
  <!-- MyBantu Nginx Reverse Proxy Logs -->
  <localfile>
    <log_format>apache</log_format>
    <location>/var/log/mybantu-nginx/access.log</location>
  </localfile>

  <localfile>
    <log_format>apache</log_format>
    <location>/var/log/mybantu-nginx/error.log</location>
  </localfile>

  <!-- MyBantu Laravel Structured Security JSON Log -->
  <localfile>
    <log_format>json</log_format>
    <location>/var/log/mybantu-app/security-json.log</location>
  </localfile>
```
- **`<log_format>apache</log_format>`:** Format standar untuk mem-parse log Nginx *combined* menggunakan decoder bawaan `web-accesslog`.
- **`<log_format>json</log_format>`:** Format JSON native yang langsung memecah seluruh objek `context.*` dari Laravel (`email`, `guard`, `endpoint`, `ip_address`, `reason`, `attempts_in_window`) menjadi field terindeks di Wazuh.

---

## 5. Pengaturan Custom Rule ID Wazuh (`local_rules.xml`)

File `docker/wazuh/config/wazuh_cluster/local_rules.xml` menggunakan rentang ID `100000`–`120000` (standar resmi Wazuh untuk *user-defined rules*).

### 5.1 Isi Lengkap `local_rules.xml`

```xml
<!-- Local rules for MyBantu Nginx & Laravel Brute Force Monitoring -->

<group name="web,appsec,mybantu,">
  <rule id="100101" level="5">
    <if_group>web</if_group>
    <regex>POST /login|POST /admin/login</regex>
    <description>MyBantu Nginx: Login POST request detected on $(url).</description>
    <group>authentication_attempt,</group>
  </rule>

  <rule id="100102" level="10" frequency="5" timeframe="60">
    <if_matched_sid>100101</if_matched_sid>
    <same_source_ip />
    <description>MyBantu Nginx: Brute force attack detected (5+ login POST requests within 60s from same IP).</description>
    <mitre>
      <id>T1110</id>
    </mitre>
    <group>attack,authentication_failures,brute_force,</group>
  </rule>

  <rule id="100103" level="12">
    <if_group>web</if_group>
    <id>^429$</id>
    <description>MyBantu Nginx: Brute force request blocked with HTTP 429 Too Many Requests.</description>
    <mitre>
      <id>T1110</id>
    </mitre>
    <group>attack,authentication_failures,brute_force,</group>
  </rule>
</group>

<group name="mybantu,authentication,">
  <rule id="100201" level="5">
    <decoded_as>json</decoded_as>
    <field name="context.event_type">^failed_login$</field>
    <description>MyBantu App: Failed login for $(context.email) from $(context.ip_address) on $(context.endpoint) ($(context.guard)).</description>
    <group>authentication_failed,</group>
  </rule>

  <rule id="100202" level="10">
    <decoded_as>json</decoded_as>
    <field name="context.event_type">^rate_limited$</field>
    <description>MyBantu App: Brute force blocked (rate_limited) for $(context.email) from $(context.ip_address) ($(context.reason)).</description>
    <mitre>
      <id>T1110</id>
    </mitre>
    <group>attack,authentication_failures,brute_force,</group>
  </rule>

  <rule id="100203" level="12" frequency="5" timeframe="60">
    <if_matched_sid>100201</if_matched_sid>
    <same_field>context.ip_address</same_field>
    <description>MyBantu App: Repeated failed login attempts (Brute Force detected) from IP $(context.ip_address).</description>
    <mitre>
      <id>T1110</id>
    </mitre>
    <group>attack,authentication_failures,brute_force,</group>
  </rule>

  <rule id="100204" level="3">
    <decoded_as>json</decoded_as>
    <field name="context.event_type">^login_success$</field>
    <description>MyBantu App: Successful login for $(context.email) from $(context.ip_address) on $(context.endpoint).</description>
    <group>authentication_success,</group>
  </rule>
</group>
```

### 5.2 Tabel Referensi Parameter & Rule ID

| Rule ID | Layer | Level | Atribut Korelasi | MITRE ATT&CK | Keterangan |
| :---: | :--- | :---: | :--- | :---: | :--- |
| `100101` | Nginx | `5` | `if_group=web`, `POST /login\|/admin/login` | — | Deteksi 1x percobaan POST ke halaman login. |
| `100102` | Nginx | `10` | `if_matched_sid=100101`, `frequency=5`, `timeframe=60`, `same_source_ip` | `T1110` | Korelasi serangan *brute force* di Nginx (5+ POST dalam 60 detik dari IP sama). |
| `100103` | Nginx | `12` | `if_group=web`, HTTP status `429` | `T1110` | Alert kritis: request serangan diblokir dengan HTTP `429`. |
| `100201` | Laravel | `5` | `decoded_as=json`, `context.event_type=failed_login` | — | 1x login gagal lengkap dengan email, guard, dan IP. |
| `100202` | Laravel | `10` | `decoded_as=json`, `context.event_type=rate_limited` | `T1110` | Pemblokiran aktif oleh `BruteForceMonitor` (`account`/`ip_rate_limit_exceeded`). |
| `100203` | Laravel | `12` | `if_matched_sid=100201`, `frequency=5`, `timeframe=60`, `same_field=context.ip_address` | `T1110` | Korelasi 5+ kegagalan autentikasi beruntun dari IP sama di level aplikasi. |
| `100204` | Laravel | `3` | `decoded_as=json`, `context.event_type=login_success` | — | Audit trail login berhasil. |

---

## 6. Pengaturan Pendukung di Sisi Laravel

Agar integrasi dengan Nginx & Wazuh berjalan penuh:
1. **`bootstrap/app.php`**:
   ```php
   $middleware->trustProxies(at: '*');
   ```
   Memastikan `$request->ip()` membaca IP asli klien dari header `X-Forwarded-For` milik Nginx/ngrok.
2. **`config/logging.php`**:
   ```php
   'security_json' => [
       'driver' => 'single',
       'path' => storage_path('logs/security-json.log'),
       'level' => 'info',
       'formatter' => \Monolog\Formatter\JsonFormatter::class,
       'replace_placeholders' => true,
   ],
   ```
   Setiap event di `App\Services\BruteForceMonitor` ditulis dalam format 1 baris JSON ke `storage/logs/security-json.log` agar langsung dibaca oleh Wazuh.

---

## 7. Standar Prosedur Operasional (SOP)

### 7.1 Instalasi Pertama Kali (Hanya Sekali di PC Baru)
```powershell
# 1. Buat file log kosong awal agar langsung terdeteksi wazuh-logcollector
New-Item -ItemType File -Force -Path docker\wazuh\logs\nginx\access.log, docker\wazuh\logs\nginx\error.log, storage\logs\security-json.log

# 2. Generate sertifikat TLS internal Wazuh
cd docker\wazuh
docker compose -f generate-indexer-certs.yml run --rm generator

# 3. Jalankan seluruh container
docker compose up -d
```

### 7.2 Menjalankan Sistem Sehari-hari / Saat Demo
1. Nyalakan **MySQL** di Laragon dan pastikan **Docker Desktop** aktif.
2. Pastikan 4 container Wazuh & Nginx berjalan:
   ```powershell
   cd docker\wazuh
   docker compose up -d
   docker compose ps
   ```
3. Jalankan **Laravel** (Terminal 1, root project):
   ```powershell
   php artisan serve --host=0.0.0.0 --port=8000
   ```
4. *(Opsional untuk akses eksternal/demo)* Jalankan **Ngrok** ke port Nginx (Terminal 2):
   ```powershell
   ngrok http 8080
   ```

### 7.3 Perintah Verifikasi & Maintenance

| Tujuan | Perintah |
| :--- | :--- |
| Uji sintaks & reload konfigurasi Nginx | `docker exec wazuh-mybantu.nginx-1 nginx -t && docker exec wazuh-mybantu.nginx-1 nginx -s reload` |
| Cek status file log yang dipantau Wazuh | `docker exec wazuh-wazuh.manager-1 grep "Analyzing file" /var/ossec/logs/ossec.log` |
| Cek kesehatan cluster Wazuh Indexer | `curl.exe -sk -u admin:SecretPassword https://127.0.0.1:9200/_cluster/health` |
| Restart Wazuh Manager setelah edit `local_rules.xml` | `cd docker\wazuh && docker compose restart wazuh.manager` |
| Matikan seluruh stack Docker (hemat RAM) | `cd docker\wazuh && docker compose down` |

---

## 8. Troubleshooting Cepat

| Masalah | Penyebab | Solusi |
| :--- | :--- | :--- |
| `wazuh.indexer` crash dengan `OutOfMemoryError: Cannot reserve ... direct buffer memory` | Heap terlalu kecil (`512m`) sehingga direct memory hanya `256m`. | Pastikan di `docker-compose.yml` terpasang `OPENSEARCH_JAVA_OPTS=-Xms768m -Xmx768m -XX:MaxDirectMemorySize=384m`. |
| Muncul `connect() to [fdc4:...]:8000 failed (101: Network unreachable)` di `error.log` | Nginx mencoba IPv6 `host.docker.internal` sebelum fallback ke IPv4. | Aman (bukan serangan). Gunakan filter `rule.groups: mybantu` di Wazuh Threat Hunting agar hanya log MyBantu yang tampil. |
| `ngrok` gagal dengan `ERR_NGROK_334` | Masih ada sesi `ngrok` lama berjalan di background. | Jalankan `Stop-Process -Name ngrok -Force`, lalu jalankan ulang `ngrok http 8080`. |
| Dashboard Wazuh `https://127.0.0.1:5601` menampilkan `503` saat baru menyala | `wazuh.indexer` masih menyelesaikan proses booting (~45–60 detik). | Tunggu 1 menit lalu refresh browser hingga status `_cluster/health` menjadi `green`. |
