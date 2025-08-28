FROM gitpod/workspace-full

USER root
# 安装 php + 常用扩展
RUN apt-get update && apt-get install -y \
    php-cli php-mbstring php-xml php-mysql unzip curl

# 安装 composer（如果 workspace 没有）
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
