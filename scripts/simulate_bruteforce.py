#!/usr/bin/env python3
"""
Automated Brute Force Simulation Script for MyBantu (local/testing only).

Handles Laravel CSRF token extraction automatically before firing POST requests.

Examples:
  python scripts/simulate_bruteforce.py --url http://127.0.0.1:8000 --mode single --email victim@example.com
  python scripts/simulate_bruteforce.py --url http://127.0.0.1:8000 --mode single --endpoint /admin/login --email admin@example.com
  python scripts/simulate_bruteforce.py --url http://127.0.0.1:8000 --mode spray --count 18
"""

import argparse
import re
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from http.cookiejar import CookieJar


def extract_csrf_token(html: str) -> str:
    match = re.search(r'name=["\']_token["\']\s+value=["\']([^"\']+)["\']', html)
    if not match:
        match = re.search(r'<meta\s+name=["\']csrf-token["\']\s+content=["\']([^"\']+)["\']', html)
    if not match:
        raise RuntimeError("CSRF token (_token) tidak ditemukan di halaman login.")
    return match.group(1)


def build_opener() -> urllib.request.OpenerDirector:
    cookie_jar = CookieJar()
    return urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cookie_jar))


def fetch_csrf(opener: urllib.request.OpenerDirector, login_url: str) -> str:
    req = urllib.request.Request(login_url, headers={"User-Agent": "MyBantu-BruteForceSim/1.0"})
    with opener.open(req, timeout=10) as resp:
        html = resp.read().decode("utf-8", errors="ignore")
    return extract_csrf_token(html)


def send_login(
    opener: urllib.request.OpenerDirector,
    login_url: str,
    token: str,
    email: str,
    password: str,
    role: str | None,
) -> int:
    payload = {
        "_token": token,
        "email": email,
        "password": password,
    }
    if role and "/admin/login" not in login_url:
        payload["role"] = role

    data = urllib.parse.urlencode(payload).encode("utf-8")
    req = urllib.request.Request(
        login_url,
        data=data,
        method="POST",
        headers={
            "User-Agent": "MyBantu-BruteForceSim/1.0",
            "Content-Type": "application/x-www-form-urlencoded",
            "Referer": login_url,
        },
    )
    try:
        with opener.open(req, timeout=10) as resp:
            return resp.status
    except urllib.error.HTTPError as err:
        return err.code


def main() -> int:
    parser = argparse.ArgumentParser(description="Simulasi Brute Force Login MyBantu")
    parser.add_argument("--url", default="http://127.0.0.1:8000", help="Base URL aplikasi")
    parser.add_argument("--endpoint", default="/login", choices=["/login", "/admin/login"], help="Target endpoint")
    parser.add_argument("--mode", default="single", choices=["single", "spray"], help="Mode simulasi")
    parser.add_argument("--email", default="victim@example.com", help="Target email (mode single)")
    parser.add_argument("--role", default="user", choices=["user", "worker", "admin"], help="Role untuk /login")
    parser.add_argument("--count", type=int, default=8, help="Jumlah percobaan")
    parser.add_argument("--delay", type=float, default=0.1, help="Jeda antar request (detik)")
    args = parser.parse_args()

    login_url = args.url.rstrip("/") + args.endpoint
    opener = build_opener()

    print(f"[*] Target: {login_url} | Mode: {args.mode} | Percobaan: {args.count}")
    token = fetch_csrf(opener, login_url)

    for i in range(1, args.count + 1):
        email = args.email if args.mode == "single" else f"spray_{i}@example.com"
        password = f"salah_{i}_123"
        status = send_login(opener, login_url, token, email, password, args.role)
        label = "BLOCKED (Rate Limited)" if status == 429 else f"HTTP {status}"
        print(f"[{i:02d}/{args.count:02d}] email={email} -> {label}")
        time.sleep(args.delay)

    print("[*] Simulasi selesai. Cek /admin/security-logs dan storage/logs/security-*.log.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
