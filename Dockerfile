FROM php:latest

WORKDIR /app

COPY *.php ./
COPY *.txt ./

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000"]
