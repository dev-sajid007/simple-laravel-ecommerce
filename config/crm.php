<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CRM Integration Settings
    |--------------------------------------------------------------------------
    |
    | Configuration settings for CRM integration service. This allows the
    | ecommerce application to synchronize customer data, orders, and
    | behavior tracking with external CRM systems.
    |
    */

    /**
     * Enable or disable CRM integration.
     */
    'enabled' => env('CRM_ENABLED', false),

    /**
     * CRM API URL.
     */
    'api_url' => env('CRM_API_URL', 'https://api.example-crm.com'),

    /**
     * CRM API Key for authentication.
     */
    'api_key' => env('CRM_API_KEY', ''),

    /**
     * CRM API Secret for additional security.
     */
    'api_secret' => env('CRM_API_SECRET', ''),

    /**
     * Connection timeout in seconds.
     */
    'timeout' => env('CRM_TIMEOUT', 10),

    /**
     * Retry attempts for failed requests.
     */
    'retry_attempts' => env('CRM_RETRY_ATTEMPTS', 3),

    /**
     * Enable/disable specific tracking events.
     */
    'tracking' => [
        'customer_registration' => env('CRM_TRACK_REGISTRATION', true),
        'order_creation' => env('CRM_TRACK_ORDERS', true),
        'product_views' => env('CRM_TRACK_PRODUCT_VIEWS', true),
        'cart_abandonment' => env('CRM_TRACK_CART_ABANDONMENT', true),
        'customer_sync' => env('CRM_SYNC_CUSTOMERS', true),
    ],

    /**
     * Cart abandonment settings.
     */
    'cart_abandonment' => [
        /**
         * Time in minutes after which a cart is considered abandoned.
         */
        'timeout_minutes' => env('CRM_CART_ABANDONMENT_TIMEOUT', 30),

        /**
         * Minimum cart value to track abandonment.
         */
        'minimum_value' => env('CRM_CART_ABANDONMENT_MIN_VALUE', 10.00),
    ],

    /**
     * Batch processing settings for high-volume operations.
     */
    'batch' => [
        /**
         * Enable batch processing for events.
         */
        'enabled' => env('CRM_BATCH_ENABLED', false),

        /**
         * Batch size for bulk operations.
         */
        'size' => env('CRM_BATCH_SIZE', 100),

        /**
         * Interval in minutes for processing batches.
         */
        'interval_minutes' => env('CRM_BATCH_INTERVAL', 5),
    ],

    /**
     * Queue settings for asynchronous processing.
     */
    'queue' => [
        /**
         * Enable queue processing for CRM events.
         */
        'enabled' => env('CRM_QUEUE_ENABLED', true),

        /**
         * Queue connection to use.
         */
        'connection' => env('CRM_QUEUE_CONNECTION', 'default'),

        /**
         * Queue name for CRM events.
         */
        'name' => env('CRM_QUEUE_NAME', 'crm-events'),

        /**
         * Delay in seconds before processing queued events.
         */
        'delay' => env('CRM_QUEUE_DELAY', 0),
    ],

    /**
     * Logging settings for CRM operations.
     */
    'logging' => [
        /**
         * Log level for CRM operations.
         */
        'level' => env('CRM_LOG_LEVEL', 'info'),

        /**
         * Log successful operations.
         */
        'log_success' => env('CRM_LOG_SUCCESS', false),

        /**
         * Log failed operations.
         */
        'log_failures' => env('CRM_LOG_FAILURES', true),
    ],

];