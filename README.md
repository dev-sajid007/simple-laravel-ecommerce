# Laravel Ecommerce Application with CRM Integration

A complete Laravel 10 ecommerce application featuring product management, shopping cart functionality, customer authentication, order management, and comprehensive CRM integration for tracking customer behavior and business analytics.

## 🌟 Features

### Core Ecommerce Features
- **Product Management**: Product listing, detailed views, category filtering, search functionality, and stock management
- **Shopping Cart**: Add/remove items, quantity updates, session-based cart for guests, persistent cart for authenticated users
- **Customer Authentication**: Registration, login, customer dashboard, profile management, and order history
- **Order Management**: Complete checkout process, order placement, tracking, and payment status management
- **Responsive Design**: Bootstrap 5 frontend with mobile-first responsive design

### CRM Integration
- **Customer Registration Tracking**: Automatically track new customer registrations with marketing preferences
- **Order Creation Tracking**: Real-time order data synchronization with complete line item details
- **Product View Tracking**: Monitor customer browsing behavior and product interests
- **Cart Abandonment Tracking**: Identify and track abandoned carts for remarketing opportunities
- **Customer Data Synchronization**: Keep customer lifetime value and order statistics up-to-date

### API Integration
- **RESTful API**: Complete API endpoints for mobile applications and external integrations
- **Authentication**: Sanctum-based API authentication with token management
- **API Documentation**: Well-structured API endpoints for all core functionalities

## 🛠️ Technology Stack

- **Backend**: Laravel 10.x
- **Frontend**: Bootstrap 5, jQuery
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **HTTP Client**: Guzzle for CRM API calls
- **Testing**: PHPUnit

## 📋 Requirements

- PHP 8.1 or higher
- Composer
- MySQL 5.7+ or MariaDB
- Node.js & NPM (for asset compilation)

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/dev-sajid007/simple-laravel-ecommerce.git
   cd simple-laravel-ecommerce
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure your database** in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel_ecommerce
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Configure CRM integration** in `.env`:
   ```env
   CRM_ENABLED=true
   CRM_API_URL=https://api.your-crm.com
   CRM_API_KEY=your_api_key
   CRM_API_SECRET=your_api_secret
   ```

6. **Run migrations and seed data**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Create storage symlink**
   ```bash
   php artisan storage:link
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to access the application.

## 🗄️ Database Schema

### Core Tables
- **products**: Product catalog with pricing, stock, and categorization
- **customers**: Customer accounts with profile and marketing preferences
- **orders**: Order records with billing/shipping addresses and status tracking
- **order_items**: Individual line items for each order
- **carts**: Shopping cart items for both guests and authenticated users

### Key Relationships
- One-to-Many: Customer → Orders
- One-to-Many: Order → Order Items
- Many-to-One: Order Item → Product
- One-to-Many: Customer → Cart Items

## 🔗 API Endpoints

### Products
- `GET /api/v1/products` - List products with pagination and filtering
- `GET /api/v1/products/{id}` - Get product details
- `GET /api/v1/categories` - List product categories

### Authentication
- `POST /api/v1/customer/register` - Customer registration
- `POST /api/v1/customer/login` - Customer login
- `GET /api/v1/customer/profile` - Get customer profile
- `PUT /api/v1/customer/profile` - Update customer profile

### Cart Management
- `GET /api/v1/cart` - Get cart contents
- `POST /api/v1/cart/add` - Add item to cart
- `PUT /api/v1/cart/update/{id}` - Update cart item quantity
- `DELETE /api/v1/cart/remove/{id}` - Remove item from cart

### Orders
- `POST /api/v1/orders/place` - Place an order
- `GET /api/v1/orders/{orderNumber}/track` - Track order status

## 🔐 Demo Credentials

- **Email**: demo@example.com
- **Password**: password

## 🎯 CRM Integration Features

### Customer Tracking
```php
// Track customer registration
$crmService->trackCustomerRegistration($customer);

// Track order creation
$crmService->trackOrderCreation($order);

// Track product views
$crmService->trackProductView($product, $customer, $sessionId);

// Track cart abandonment
$crmService->trackCartAbandonment($customer, $sessionId, $cartItems);
```

### Configuration Options
- Enable/disable specific tracking events
- Configure cart abandonment timeout
- Set API timeout and retry settings
- Batch processing for high-volume operations

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

Run specific tests:
```bash
php artisan test --filter=HomePageTest
```

## 🏗️ Architecture

### MVC Structure
- **Models**: Eloquent models with relationships and business logic
- **Views**: Blade templates with Bootstrap 5 components
- **Controllers**: RESTful controllers for web and API routes

### Services
- **CrmService**: Handles all CRM integration logic
- **Cart Management**: Session and database-based cart handling
- **Order Processing**: Complete order workflow with stock management

### Security Features
- CSRF protection
- SQL injection prevention
- XSS protection
- Authentication middleware
- Form validation

## 📱 Frontend Features

### Customer Interface
- Product browsing and search
- Shopping cart management
- User registration and authentication
- Order placement and tracking
- Customer dashboard and profile management

### Responsive Design
- Mobile-first Bootstrap 5 framework
- Touch-friendly interface
- Optimized for all screen sizes

## 🔧 Configuration

### CRM Settings
All CRM integration settings can be configured in `config/crm.php`:
- API endpoints and authentication
- Tracking event configuration
- Batch processing settings
- Queue configuration for async processing

### Environment Variables
Key environment variables for customization:
- `CRM_ENABLED`: Enable/disable CRM integration
- `CRM_API_URL`: CRM API base URL
- `CRM_API_KEY`: API authentication key
- `SESSION_DRIVER`: Session storage driver
- `QUEUE_CONNECTION`: Queue driver for async processing

## 📈 Performance

### Optimization Features
- Database query optimization with proper indexing
- Eager loading for relationships
- Pagination for large datasets
- Caching for frequently accessed data
- Async processing for CRM events

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙋‍♂️ Support

For support and questions:
- Create an issue on GitHub
- Check the documentation
- Review the test suite for usage examples

---

**Built with ❤️ using Laravel 10 and modern web technologies**