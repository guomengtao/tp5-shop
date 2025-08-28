FROM gitpod/workspace-full

# 安装 PHP 扩展
RUN sudo apt-get update && sudo apt-get install -y \
    php-mysql \
    php-sqlite3 \
    php-gd \
    php-xml \
    php-mbstring \
    php-curl \
    unzip \
    && sudo rm -rf /var/lib/apt/lists/*

# 安装 Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
