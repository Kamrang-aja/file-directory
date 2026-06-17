# GIC Repository

Enterprise Repository Browser untuk mendistribusikan software, firmware, ISO installer, dokumentasi, backup tools, dan resource operasional perusahaan.

![PHP](https://img.shields.io/badge/PHP-8%2B-blue)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)
![License](https://img.shields.io/badge/License-Internal-green)

---

## Overview

GIC Repository adalah aplikasi repository berbasis PHP yang ringan, tanpa database, dan dapat digunakan untuk menampilkan file repository yang tersimpan secara lokal maupun pada storage eksternal seperti TrueNAS melalui NFS mount.

<img width="1355" height="647" alt="image" src="https://github.com/user-attachments/assets/ad84810c-a5ac-419c-b368-67cfca6b43ca" />

Dirancang untuk kebutuhan internal perusahaan dalam mendistribusikan:

- Software Installer
- ISO Images
- Firmware
- Documentation
- Backup Tools
- Virtualization Resources
- Network Resources

---

## Features

- No Database Required
- Auto Scan Repository Folder
- Recursive Folder Navigation
- File Download Support
- Search File & Folder
- Light / Dark Mode
- Responsive Bootstrap 5 UI
- Mobile Friendly
- Last Modified Date
- Human Readable File Size
- TrueNAS Storage Support
- Local Storage Support
- Directory Traversal Protection
- Clean Repository Index Interface

---

## Repository Structure

```text
repository/
├── Linux/
├── Mikrotik/
├── Proxmox/
├── VMware/
├── Documentation/
└── Others/
```

---

## Requirements

### Server

- PHP 8.0+
- Apache 2.4+
  atau
- Nginx 1.18+
- Linux Server (Recommended)

### Optional

- TrueNAS Scale / Core
- NFS Share
- SSD Storage

---

## Storage Configuration

### Local Storage

Repository disimpan langsung pada server web.

```text
/var/www/html/file-directory/repository
```

### TrueNAS Storage

Repository dapat dibaca langsung dari mount NFS TrueNAS.

Contoh mount:

```bash
sudo mkdir -p /mnt/truenas

sudo mount -t nfs \
192.168.3.13:/mnt/tank/repository \
/mnt/truenas/repository
```

Konfigurasi pada aplikasi:

```php
$truenasPath = '/mnt/truenas/repository';
$localPath   = __DIR__ . '/repository';
```

---

## Installation

### Clone Repository

```bash
git clone https://github.com/your-repository/file-repository.git

cd file-repository
```

### Set Permission

```bash
sudo chown -R www-data:www-data .

sudo chmod -R 755 .
```

---

## Apache Configuration

DocumentRoot:

```text
/var/www/html/file-repository
```

### HTTP Redirect to HTTPS

```apache
<VirtualHost *:80>

    ServerName repo.domain.com

    Redirect permanent / https://repo.domain.com/

</VirtualHost>
```

### HTTPS Virtual Host

```apache
<VirtualHost *:443>

    ServerName repo.domain.com

    DocumentRoot /var/www/html/gic-repository

    SSLEngine on

    SSLCertificateFile /etc/letsencrypt/live/repo.domain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/repo.domain.com/privkey.pem

    <Directory /var/www/html/file-repository>
        AllowOverride All
        Require all granted
    </Directory>

    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"

</VirtualHost>
```

### Generate SSL Certificate

```bash
sudo apt install certbot python3-certbot-apache -y

sudo certbot --apache -d repo.domain.com
```

Restart Apache:

```bash
sudo systemctl restart apache2
```

---

## Nginx Configuration

### HTTP Redirect to HTTPS

```nginx
server {

    listen 80;
    listen [::]:80;

    server_name repo.domain.com;

    return 301 https://$host$request_uri;
}
```

### HTTPS Virtual Host

```nginx
server {

    listen 443 ssl http2;
    listen [::]:443 ssl http2;

    server_name repo.domain.com;

    root /var/www/html/file-repository;

    index index.php;

    ssl_certificate     /etc/letsencrypt/live/repo.domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/repo.domain.com/privkey.pem;

    ssl_protocols TLSv1.2 TLSv1.3;

    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {

        include fastcgi_params;

        fastcgi_pass unix:/run/php/php8.3-fpm.sock;

        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

    }
}
```

### Generate SSL Certificate

```bash
sudo apt install certbot python3-certbot-nginx -y

sudo certbot --nginx -d repo.domain.com
```

Restart Nginx:

```bash
sudo systemctl restart nginx
```

### SSL Auto Renewal

```bash
sudo certbot renew --dry-run
```

---

## Security

Aplikasi telah dilengkapi dengan:

- Path Validation
- Directory Traversal Protection
- Realpath Verification
- Safe File Download Handling
- Read Only Repository Access

---

## Technology Stack

- PHP 8+
- Bootstrap 5.3
- Bootstrap Icons
- JavaScript (Vanilla)
- HTML5
- CSS3

---

## Author

Kamrang
Infrastructure Engineer
PT. Global Inti Corporatama

---

## License

Internal Use Only
Copyright © 2026 Kamrang
All Rights Reserved.
