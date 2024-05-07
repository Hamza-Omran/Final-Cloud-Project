# Use the php:apache base image
FROM php:7.4-apache

# Install the mysqli extension
RUN docker-php-ext-install mysqli 
    # Restart Apache to apply changes
   # && service apache2 restart

# Expose port 80
EXPOSE 80
